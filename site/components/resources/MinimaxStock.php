<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxStock — текущие остатки товаров на складах (само за читање).
 *
 * Контекст:
 *   Stock — это представление текущих складских остатков. Не является
 *   самостоятельным документом — остатки формируются автоматически на
 *   основе StockEntry (приходов и расходов). Через этот модуль можно
 *   узнать количество, стоимость, среднюю цену закупки и цену продажи
 *   по каждой позиции (в разрезе склада, партии, на конкретную дату).
 *
 * Эндпоинты:
 *   GET api/orgs/{orgId}/stocks           — список остатков
 *   GET api/orgs/{orgId}/stocks/{itemId}  — остаток по конкретной позиции
 *
 * Структура StockListItem:
 * {
 *   "Item":                 { "ID": 10, "Name": "Konzultantske usluge" },
 *   "ItemName":             "Konzultantske usluge",
 *   "ItemCode":             "U-001",
 *   "ItemEANCode":          "",
 *   "UnitOfMeasurement":    "kom",
 *   "AveragePurchasePrice": 1200.00,
 *   "SellingPrice":         2000.00,
 *   "Quantity":             50.0,
 *   "Value":                60000.00,
 *   "BatchNumber":          null,          // если ResultsByBatchNumber включён
 *   "Currency":             { "ID": 1, "Name": "RSD" }
 * }
 *
 * Использование:
 * ```php
 * $stock = Yii::$app->minimax->stock($orgId);
 *
 * // Все остатки на складе
 * $all = $stock->list(['WarehouseId' => 1]);
 *
 * // Остаток конкретной позиции по всем складам
 * $item = $stock->getForItem(10);
 *
 * // Остаток на конкретную дату
 * $historical = $stock->getForItem(10, date: '2024-01-01');
 *
 * // Все позиции (включая ранее бывшие на складе, сейчас нулевые)
 * $all = $stock->list(['Mode' => 1]);
 *
 * // Карта [ItemId => Quantity] для быстрой проверки наличия
 * $map = $stock->getQuantityMap(warehouseId: 1);
 * ```
 */
class MinimaxStock extends MinimaxResource
{
    protected function getResourceName(): string
    {
        return 'stocks';
    }

    // -----------------------------------------------------------------
    // Методы чтения
    // -----------------------------------------------------------------

    /**
     * Список остатков с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - ItemID              : ID позиции
     *   - ItemTitle           : поиск по названию
     *   - ItemIdentifier      : поиск по коду
     *   - ItemEANCode         : поиск по EAN
     *   - ItemType            : тип позиции (B/M/P/I/S/A/AS)
     *   - WarehouseId         : ID склада
     *   - BatchNumber         : номер партии
     *   - Date                : остатки на дату (формат: 'YYYY-MM-DD')
     *   - ResultsByBatchNumber: группировка по партиям
     *   - Mode                : 1 = включить позиции с нулевым остатком
     *   - CurrentPage         : номер страницы (с 1)
     *   - PageSize            : размер страницы
     *   - SortField           : поле сортировки
     *   - Order               : A / D
     *
     * @param  array $params
     * @return array SearchResult { Rows: [StockListItem], TotalRows, ... }
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 100], $params);

        return $this->getClient()->get($this->buildPath(), $params);
    }

    /**
     * Остаток конкретной позиции (с опциональной фильтрацией).
     *
     * GET api/orgs/{orgId}/stocks/{itemId}
     *
     * @param  int|string      $itemId
     * @param  int|string|null $warehouseId  Фильтр по складу
     * @param  string|null     $batchNumber  Фильтр по партии
     * @param  string|null     $date         Остаток на дату ('YYYY-MM-DD')
     * @return array           StockListItem
     * @throws MinimaxApiException
     */
    public function getForItem(
        int|string      $itemId,
        int|string|null $warehouseId  = null,
        string|null     $batchNumber  = null,
        string|null     $date         = null,
    ): array {
        $params = array_filter([
            'WarehouseId' => $warehouseId,
            'BatchNumber' => $batchNumber,
            'Date'        => $date,
        ], fn($v) => $v !== null);

        return $this->getClient()->get($this->buildPath($itemId), $params);
    }

    // -----------------------------------------------------------------
    // Хелперы
    // -----------------------------------------------------------------

    /**
     * Карта [ItemId => Quantity] для быстрой проверки наличия.
     *
     * @param  int|string|null $warehouseId  Фильтр по складу
     * @param  string|null     $date         Остатки на дату
     * @param  int             $pageSize
     * @return array<int, float>
     * @throws MinimaxApiException
     */
    public function getQuantityMap(
        int|string|null $warehouseId = null,
        string|null     $date        = null,
        int             $pageSize    = 500,
    ): array {
        $params = array_filter([
            'WarehouseId' => $warehouseId,
            'Date'        => $date,
            'PageSize'    => $pageSize,
        ], fn($v) => $v !== null);

        $result = $this->list($params);
        $map    = [];

        foreach ($result['Rows'] as $row) {
            $id       = $row['Item']['ID'] ?? null;
            $quantity = $row['Quantity'] ?? 0;
            if ($id !== null) {
                $map[$id] = (float) $quantity;
            }
        }

        return $map;
    }

    /**
     * Проверить, есть ли позиция в наличии (остаток > 0).
     *
     * @throws MinimaxApiException
     */
    public function isInStock(
        int|string      $itemId,
        int|string|null $warehouseId = null,
    ): bool {
        $result   = $this->getForItem($itemId, warehouseId: $warehouseId);
        $quantity = $result['Quantity'] ?? 0;

        return (float) $quantity > 0;
    }

    // -----------------------------------------------------------------
    // Заблокированные методы — Stock только для чтения
    // -----------------------------------------------------------------

    public function create(array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxStock: остатки формируются автоматически через StockEntry.'
        );
    }

    public function update(int|string $id, array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxStock: остатки доступны только для чтения.'
        );
    }

    public function delete(int|string $id): array
    {
        throw new \BadMethodCallException(
            'MinimaxStock: остатки доступны только для чтения.'
        );
    }
}
