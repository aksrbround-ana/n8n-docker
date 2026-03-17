<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxStockEntry — складские проводки (примке, издатнице, отпремнице).
 *
 * Контекст:
 *   StockEntry — это документ складской операции: приход (P), расход (I)
 *   или собственное потребление (L). Каждая проводка влияет на остатки
 *   в MinimaxStock. Подтверждённые проводки нельзя редактировать —
 *   необходимо сначала отменить подтверждение.
 *
 *   StockEntry также используется для формирования отпремницы (накладной
 *   на отгрузку), которую можно получить в PDF через getDeliveryNotePdf.
 *
 *   Жизненный цикл: Черновик (O) ⇄ Подтверждён (P)
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/stockentry                                          — список
 *   GET    api/orgs/{orgId}/stockentry/{id}                                     — по ID
 *   GET    api/orgs/{orgId}/stockentry/{id}/actions/getDeliveryNotepdf          — PDF накладной
 *   POST   api/orgs/{orgId}/stockentry                                          — создать
 *   PUT    api/orgs/{orgId}/stockentry/{id}                                     — обновить
 *   PUT    api/orgs/{orgId}/stockentry/{id}/actions/{actionName}                — custom action
 *   DELETE api/orgs/{orgId}/stockentry/{id}                                     — удалить
 *
 * Структура объекта StockEntry (сокращённо):
 * {
 *   "StockEntryId":       1,
 *   "StockEntryType":     "P",         // тип, см. TYPE_*
 *   "StockEntrySubtype":  "S",         // подтип, см. SUBTYPE_*
 *   "ReceiptFromFarmer":  null,
 *   "Date":               "2024-06-01T00:00:00",
 *   "Number":             42,          // readonly
 *   "Customer":           { "ID": 456, "Name": "Dobavljač d.o.o." },
 *   "Analytic":           null,
 *   "Rabate":             0.0,
 *   "Description":        "Prijem robe",
 *   "ValueOfMaterialAndGoods": 50000.00,
 *   "ValueOfRelatedCosts":     0.00,
 *   "PercentOfDirectCostsOfPurchase": 0.0,
 *   "ValueOfReceipt":     50000.00,
 *   "Currency":           { "ID": 1, "Name": "RSD" },
 *   "ExchangeRate":       1.0,
 *   "Status":             "O",         // readonly, O=черновик, P=подтверждён
 *   "Account":            null,
 *   "AssociationWithIssuedInvoice": "N",
 *   "DeliveryNoteReportTemplate": null,
 *   "AddresseeName":      "",
 *   "RecipientName":      "",
 *   "StockEntryRows": [
 *     {
 *       "StockEntryRowId": 1,
 *       "Item":            { "ID": 10, "Name": "Artikal" },
 *       "Warehouse":       { "ID": 1,  "Name": "Centralno" },
 *       "ItemName":        "Artikal",
 *       "ItemCode":        "A-001",
 *       "Quantity":        100.0,
 *       "PurchasePrice":   500.00,
 *       "SellingPrice":    800.00,
 *       "UnitOfMeasurement": "kom",
 *       "RowVersion":      "AAA...="
 *     }
 *   ],
 *   "RowVersion": "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $entries = Yii::$app->minimax->stockEntry($orgId);
 *
 * // Создать приход товара
 * $entry = $entries->create([
 *     'StockEntryType'    => MinimaxStockEntry::TYPE_RECEIPT,
 *     'StockEntrySubtype' => MinimaxStockEntry::SUBTYPE_SUPPLIER,
 *     'Date'              => '2024-06-01T00:00:00',
 *     'Customer'          => ['ID' => 456],      // поставщик
 *     'Description'       => 'Prijem robe juni',
 *     'StockEntryRows'    => [
 *         [
 *             'Item'             => ['ID' => 10],
 *             'Warehouse'        => ['ID' => 1],
 *             'Quantity'         => 100.0,
 *             'PurchasePrice'    => 500.00,
 *             'UnitOfMeasurement'=> 'kom',
 *         ],
 *     ],
 * ]);
 *
 * // Подтвердить приход
 * $entries->confirm($entry['StockEntryId'], $entry['RowVersion']);
 *
 * // Получить PDF отпремницы
 * $entries->saveDeliveryNotePdf(1, $rowVersion, '/tmp/otpremnica.pdf');
 * ```
 */
class MinimaxStockEntry extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы StockEntryType
    // -----------------------------------------------------------------

    /** Приход (primka) */
    public const TYPE_RECEIPT  = 'P';
    /** Расход / отгрузка (izdatnica) */
    public const TYPE_ISSUE    = 'I';
    /** Собственное потребление */
    public const TYPE_OWN_USE  = 'L';

    // -----------------------------------------------------------------
    // Константы StockEntrySubtype
    // -----------------------------------------------------------------

    /** Поставщик / клиент */
    public const SUBTYPE_SUPPLIER  = 'S';
    /** Производство */
    public const SUBTYPE_PRODUCTION = 'P';
    /** Межскладское перемещение */
    public const SUBTYPE_STORAGE   = 'L';
    /** Перемещение на клиента / поставщика */
    public const SUBTYPE_STORAGE_TO_CLIENT = 'R';

    // -----------------------------------------------------------------
    // Константы Status (readonly)
    // -----------------------------------------------------------------

    /** Черновик */
    public const STATUS_DRAFT     = 'O';
    /** Подтверждён */
    public const STATUS_CONFIRMED = 'P';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'stockentry';
    }

    // -----------------------------------------------------------------
    // Стандартные методы
    // -----------------------------------------------------------------

    /**
     * Список складских проводок с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - StockEntryType    : тип проводки (TYPE_*)
     *   - StockEntrySubtype : подтип (SUBTYPE_*)
     *   - Status            : статус (STATUS_*)
     *   - WarehouseId       : ID склада
     *   - CustomerId        : ID клиента/поставщика
     *   - DateFrom          : дата от
     *   - DateTo            : дата до
     *   - CurrentPage       : номер страницы (с 1)
     *   - PageSize          : размер страницы
     *   - SortField         : поле сортировки
     *   - Order             : A / D
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
    // GET custom action
    // -----------------------------------------------------------------

    /**
     * Получить PDF отпремницы (накладной на отгрузку) — base64.
     *
     * GET api/orgs/{orgId}/stockentry/{id}/actions/getDeliveryNotepdf?rowVersion=...
     *
     * @throws MinimaxApiException
     */
    public function deliveryNotePdf(int|string $id, string $rowVersion): array
    {
        return $this->getClient()->get(
            $this->buildPath($id, 'actions/getDeliveryNotepdf'),
            ['rowVersion' => $rowVersion]
        );
    }

    /**
     * Сохранить PDF отпремницы на диск.
     *
     * @throws \RuntimeException
     * @throws MinimaxApiException
     */
    public function saveDeliveryNotePdf(
        int|string $id,
        string     $rowVersion,
        string     $targetPath,
    ): string {
        $result  = $this->deliveryNotePdf($id, $rowVersion);
        $content = base64_decode($result['Data'] ?? $result['Content'] ?? '');

        if ($content === false || $content === '') {
            throw new \RuntimeException(
                "MinimaxStockEntry: не удалось декодировать PDF отпремницы {$id}."
            );
        }

        if (file_put_contents($targetPath, $content) === false) {
            throw new \RuntimeException(
                "MinimaxStockEntry: не удалось записать файл в {$targetPath}."
            );
        }

        return $targetPath;
    }

    // -----------------------------------------------------------------
    // PUT custom actions
    // -----------------------------------------------------------------

    /**
     * Подтвердить складскую проводку (O → P).
     *
     * @throws MinimaxApiException
     */
    public function confirm(int|string $id, string $rowVersion): array
    {
        return $this->getClient()->put(
            $this->buildPath($id, 'actions/confirm'),
            [],
            ['rowVersion' => $rowVersion]
        );
    }

    /**
     * Отменить подтверждение складской проводки (P → O).
     *
     * @throws MinimaxApiException
     */
    public function cancelConfirmation(int|string $id, string $rowVersion): array
    {
        return $this->getClient()->put(
            $this->buildPath($id, 'actions/cancelConfirmation'),
            [],
            ['rowVersion' => $rowVersion]
        );
    }

    // -----------------------------------------------------------------
    // Хелперы
    // -----------------------------------------------------------------

    /**
     * Список черновиков проводок.
     *
     * @throws MinimaxApiException
     */
    public function listDrafts(string|null $type = null, int $pageSize = 50): array
    {
        $params = ['Status' => self::STATUS_DRAFT, 'PageSize' => $pageSize];

        if ($type !== null) {
            $params['StockEntryType'] = $type;
        }

        return $this->list($params);
    }

    /**
     * Список проводок за период.
     *
     * @throws MinimaxApiException
     */
    public function listByPeriod(
        string      $dateFrom,
        string      $dateTo,
        string|null $type     = null,
        int         $pageSize = 100,
    ): array {
        $params = [
            'DateFrom' => $dateFrom,
            'DateTo'   => $dateTo,
            'PageSize' => $pageSize,
        ];

        if ($type !== null) {
            $params['StockEntryType'] = $type;
        }

        return $this->list($params);
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
