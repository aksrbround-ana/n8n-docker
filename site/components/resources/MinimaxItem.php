<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxItem — справочник товаров и услуг (артикли) организации.
 *
 * Контекст:
 *   Item — это позиция номенклатуры: товар, материал, полуфабрикат, готовая
 *   продукция, услуга или авансовый платёж. Используется в строках счетов
 *   (IssuedInvoice, ReceivedInvoice, Order), складских операциях (StockEntry)
 *   и ценовых списках.
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/items                        — список
 *   GET    api/orgs/{orgId}/items/{id}                   — по ID
 *   GET    api/orgs/{orgId}/items/code({code})           — по коду
 *   GET    api/orgs/{orgId}/items/itemsdata              — сводные данные (название/цена/ед.)
 *   GET    api/orgs/{orgId}/items/settings               — настройки номенклатуры
 *   GET    api/orgs/{orgId}/items/synccandidates         — для синхронизации
 *   GET    api/orgs/{orgId}/items/pricelists             — ценовые списки
 *   POST   api/orgs/{orgId}/items                        — создать
 *   PUT    api/orgs/{orgId}/items/{id}                   — обновить
 *   DELETE api/orgs/{orgId}/items/{id}                   — удалить
 *
 * Структура объекта Item:
 * {
 *   "ItemId":             1,
 *   "Name":               "Konsultantske usluge",
 *   "Code":               "U-001",
 *   "EANCode":            "",
 *   "Description":        "",
 *   "ItemType":           "S",              // тип, см. константы TYPE_*
 *   "StocksManagedOnlyByQuantity": "N",     // N=по кол-ву и стоимости, D=только по кол-ву
 *   "UnitOfMeasurement":  "sat",            // единица измерения (свободная строка)
 *   "MassPerUnit":        0.0,
 *   "ProductGroup":       { "ID": 1, "Name": "Usluge" },
 *   "VatRate":            { "ID": 2, "Name": "20%" },
 *   "Price":              5000.00,          // цена продажи без НДС
 *   "RebatePercent":      0.0,              // процент скидки
 *   "Usage":              "D",             // D=активен, N=неактивен
 *   "Currency":           { "ID": 1, "Name": "RSD" },
 *   "SerialNumbers":      "N",
 *   "BatchNumbers":       "N",
 *   "RevenueAccountDomestic":   { "ID": 10, "Name": "Prihodi od usluga" },
 *   "RevenueAccountEU":         null,
 *   "RevenueAccountOutsideEU":  null,
 *   "StocksAccount":            null,       // только для товаров/материалов
 *   "ReliefByCompositeFromWarehouse":    "N",
 *   "ReliefByCompositeFromIssuedInvoice": "N",
 *   "Composite": [],                        // составные позиции: [{ Item, Quantity }]
 *   "RowVersion": "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $items = Yii::$app->minimax->item($orgId);
 *
 * // Список активных позиций
 * $list = $items->list(['Usage' => 'D']);
 *
 * // По коду
 * $item = $items->getByCode('U-001');
 *
 * // Ценовой список для конкретного клиента
 * $prices = $items->getPriceLists(customerId: 456);
 *
 * // Ценовой список для конкретного склада (только позиции этого склада)
 * $prices = $items->getPriceLists(warehouseId: 1, mode: 1);
 *
 * // Карта [ItemId => ['Code' => ..., 'Name' => ..., 'Price' => ...]]
 * $map = $items->getCodeMap();
 *
 * // Сводные данные по позициям (лёгкий запрос без всех полей)
 * $data = $items->getItemsData(['SearchString' => 'uslug']);
 * ```
 */
class MinimaxItem extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы ItemType
    // -----------------------------------------------------------------

    /** Товар */
    public const TYPE_GOODS             = 'B';
    /** Материал */
    public const TYPE_MATERIAL          = 'M';
    /** Полуфабрикат */
    public const TYPE_SEMIFINISHED      = 'P';
    /** Готовая продукция */
    public const TYPE_PRODUCT           = 'I';
    /** Услуга */
    public const TYPE_SERVICE           = 'S';
    /** Авансовый платёж */
    public const TYPE_ADVANCE           = 'A';
    /** Авансовый платёж за услуги */
    public const TYPE_ADVANCE_SERVICE   = 'AS';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'items';
    }

    // -----------------------------------------------------------------
    // Стандартные методы
    // -----------------------------------------------------------------

    /**
     * Список позиций номенклатуры с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - SearchString  : поиск по названию или коду
     *   - ItemType      : тип позиции (TYPE_*)
     *   - Usage         : D (активные) / N (неактивные)
     *   - CurrentPage   : номер страницы (с 1)
     *   - PageSize      : размер страницы
     *   - SortField     : поле сортировки ('Name', 'Code')
     *   - Order         : A / D
     *
     * @param  array $params
     * @return array SearchResult { Rows, TotalRows, CurrentPageNumber, PageSize }
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 100], $params);

        return $this->getClient()->get($this->buildPath(), $params);
    }

    // -----------------------------------------------------------------
    // Дополнительные GET-методы
    // -----------------------------------------------------------------

    /**
     * Найти позицию по коду.
     *
     * GET api/orgs/{orgId}/items/code({code})
     *
     * @throws MinimaxApiException
     */
    public function getByCode(string $code): array
    {
        return $this->getClient()->get($this->buildPath("code({$code})"));
    }

    /**
     * Получить сводные данные позиций: название, код, цена, единица измерения.
     * Более лёгкий запрос чем list() — не возвращает счета и составные позиции.
     *
     * GET api/orgs/{orgId}/items/itemsdata
     *
     * @throws MinimaxApiException
     */
    public function getItemsData(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 100], $params);

        return $this->getClient()->get($this->buildPath('itemsdata'), $params);
    }

    /**
     * Получить настройки номенклатуры организации.
     *
     * GET api/orgs/{orgId}/items/settings
     *
     * @throws MinimaxApiException
     */
    public function getSettings(): array
    {
        return $this->getClient()->get($this->buildPath('settings'));
    }

    /**
     * Получить кандидатов для синхронизации.
     *
     * @throws MinimaxApiException
     */
    public function getSyncCandidates(array $params = []): array
    {
        return $this->getClient()->get($this->buildPath('synccandidates'), $params);
    }

    /**
     * Получить ценовой список с ценами продажи (с НДС и без).
     *
     * Можно фильтровать по складу, клиенту или конкретной позиции.
     * Mode=1 — вернуть только позиции, связанные с указанным складом/клиентом.
     *
     * GET api/orgs/{orgId}/items/pricelists
     *
     * @param  int|string|null $warehouseId  ID склада
     * @param  int|string|null $customerId   ID клиента
     * @param  int|string|null $itemId       ID конкретной позиции
     * @param  int|null        $mode         1 = только связанные позиции
     * @return array           { Rows: [{ ItemId, Name, Code, Price, PriceWithVat, ... }] }
     * @throws MinimaxApiException
     */
    public function getPriceLists(
        int|string|null $warehouseId = null,
        int|string|null $customerId  = null,
        int|string|null $itemId      = null,
        int|null        $mode        = null,
    ): array {
        $params = array_filter([
            'WarehouseId' => $warehouseId,
            'CustomerId'  => $customerId,
            'ItemId'      => $itemId,
            'Mode'        => $mode,
        ], fn($v) => $v !== null);

        return $this->getClient()->get($this->buildPath('pricelists'), $params);
    }

    // -----------------------------------------------------------------
    // Хелперы
    // -----------------------------------------------------------------

    /**
     * Карта [ItemId => ['Code' => ..., 'Name' => ..., 'Price' => ...]]
     * для активных позиций. Используется при построении строк счетов.
     *
     * @throws MinimaxApiException
     */
    public function getCodeMap(int $pageSize = 500): array
    {
        $result = $this->list(['Usage' => 'D', 'PageSize' => $pageSize]);
        $map    = [];

        foreach ($result['Rows'] as $row) {
            $map[$row['ItemId']] = [
                'Code'  => $row['Code'],
                'Name'  => $row['Name'],
                'Price' => $row['Price'],
            ];
        }

        return $map;
    }

    /**
     * Список только активных позиций заданного типа.
     *
     * @param  string $type  Константа TYPE_*
     * @throws MinimaxApiException
     */
    public function listByType(string $type, int $pageSize = 200): array
    {
        return $this->list([
            'ItemType' => $type,
            'Usage'    => 'D',
            'PageSize' => $pageSize,
        ]);
    }
}
