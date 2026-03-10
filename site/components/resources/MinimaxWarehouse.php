<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxWarehouse — складови (magacini) организации.
 *
 * Контекст:
 *   Warehouse — справочник складов организации. Каждая строка StockEntry
 *   и каждая позиция в Stock привязана к конкретному складу. Склад
 *   определяет метод учёта (по закупочной или продажной стоимости)
 *   и набор счетов бухгалтерского учёта.
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/warehouses                   — список
 *   GET    api/orgs/{orgId}/warehouses/{id}              — по ID
 *   GET    api/orgs/{orgId}/warehouses/synccandidates    — для синхронизации
 *   POST   api/orgs/{orgId}/warehouses                   — создать
 *   PUT    api/orgs/{orgId}/warehouses/{id}              — обновить
 *   DELETE api/orgs/{orgId}/warehouses/{id}              — удалить
 *
 * Структура объекта Warehouse:
 * {
 *   "WarehouseId":   1,
 *   "Code":          "MAG-01",
 *   "Name":          "Centralni magacin",
 *   "Location":      "Beograd",
 *   "InventoryManagement":        "Nabavna",   // Nabavna=закупочная, Prodajna=продажная
 *   "InventoryManagementByValue": "N",         // D=разрешить ввод продажной цены
 *   "SellingPriceInput":          "N",         // D=учёт только по стоимости
 *   "InventoryBookkeping":        "Nabavna",   // метод бухгалтерского учёта
 *   "StocksAccount":              { "ID": 1, "Name": "130" },
 *   "PDAccount":                  null,        // счёт разницы в ценах
 *   "VATStandardAccount":         null,
 *   "VATReducedAccount":          null,
 *   "VATSpecialReducedAccount":   null,
 *   "Usage":                      "D",         // D=активен, N=неактивен
 *   "RowVersion":                 "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $warehouses = Yii::$app->minimax->warehouse($orgId);
 *
 * // Список активных складов
 * $list = $warehouses->listActive();
 *
 * // Карта [WarehouseId => Name]
 * $map = $warehouses->getIdNameMap();
 *
 * // ID первого активного склада (для StockEntry, Stock, Order)
 * $id = $warehouses->getFirstId();
 * ```
 */
class MinimaxWarehouse extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы InventoryManagement / InventoryBookkeping
    // -----------------------------------------------------------------

    /** Учёт по закупочной стоимости */
    public const INVENTORY_PURCHASE = 'Nabavna';
    /** Учёт по продажной стоимости */
    public const INVENTORY_SALES    = 'Prodajna';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'warehouses';
    }

    // -----------------------------------------------------------------
    // Стандартные методы
    // -----------------------------------------------------------------

    /**
     * Список складов с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - SearchString : поиск по названию или коду
     *   - Usage        : D (активные) / N (неактивные)
     *   - CurrentPage  : номер страницы (с 1)
     *   - PageSize     : размер страницы
     *   - SortField    : поле сортировки
     *   - Order        : A / D
     *
     * @param  array $params
     * @return array SearchResult { Rows, TotalRows, CurrentPageNumber, PageSize }
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 50], $params);

        return $this->getClient()->get($this->buildPath(), $params);
    }

    // -----------------------------------------------------------------
    // Хелперы
    // -----------------------------------------------------------------

    /**
     * Список только активных складов.
     *
     * @throws MinimaxApiException
     */
    public function listActive(int $pageSize = 50): array
    {
        return $this->list(['Usage' => 'D', 'PageSize' => $pageSize]);
    }

    /**
     * Карта [WarehouseId => Name] активных складов.
     * Используется для заполнения выпадающих списков.
     *
     * @throws MinimaxApiException
     */
    public function getIdNameMap(int $pageSize = 100): array
    {
        $result = $this->listActive($pageSize);
        $map    = [];

        foreach ($result['Rows'] as $row) {
            $map[$row['WarehouseId']] = $row['Name'];
        }

        return $map;
    }

    /**
     * Получить ID первого активного склада.
     * Удобно для организаций с одним складом.
     *
     * @return int|string
     * @throws MinimaxApiException
     * @throws \RuntimeException  если складов нет
     */
    public function getFirstId(): int|string
    {
        $result = $this->listActive(pageSize: 1);
        $rows   = $result['Rows'] ?? [];

        if (empty($rows)) {
            throw new \RuntimeException(
                'MinimaxWarehouse: у организации нет активных складов.'
            );
        }

        return $rows[0]['WarehouseId'];
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
}
