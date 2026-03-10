<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxOrder — заказы (наруџбенице) организации.
 *
 * Контекст:
 *   Order — это документ заказа, который может быть двух типов:
 *   - Выставленный заказ (I): заказ, выставленный клиенту (prodajni nalog)
 *   - Полученный заказ (P): заказ, полученный от клиента/поставщика (nabavni nalog)
 *
 *   Жизненный цикл заказа:
 *   Черновик (O) → Подтверждён (P) → Завершён (Z)
 *                                  ↘ Аннулирован (R)
 *
 *   Подтверждённый заказ можно преобразовать в IssuedInvoice через
 *   action `createissuedinvoice`.
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/orders                                    — список
 *   GET    api/orgs/{orgId}/orders/{id}                               — по ID
 *   GET    api/orgs/{orgId}/orders/synccandidates                     — для синхронизации
 *   GET    api/orgs/{orgId}/orders/{id}/actions/getorderpdf           — PDF заказа
 *   POST   api/orgs/{orgId}/orders                                    — создать
 *   PUT    api/orgs/{orgId}/orders/{id}                               — обновить
 *   PUT    api/orgs/{orgId}/orders/{id}/actions/{actionName}          — custom action
 *   DELETE api/orgs/{orgId}/orders/{id}                               — удалить
 *
 * Структура объекта Order (сокращённо):
 * {
 *   "OrderId":            1,
 *   "ReceivedIssued":     "I",              // I=выставленный, P=полученный
 *   "Year":               2024,             // readonly
 *   "Number":             42,               // readonly
 *   "Date":               "2024-06-01T00:00:00",
 *   "Customer":           { "ID": 456, "Name": "Firma d.o.o." },
 *   "CustomerName":       "Firma d.o.o.",   // может отличаться от Customer.Name
 *   "CustomerAddress":    "Ulica 1",
 *   "CustomerPostalCode": "11000",
 *   "CustomerCity":       "Beograd",
 *   "CustomerCountry":    { "ID": 688, "Name": "Srbija" },
 *   "RecipientName":      "",               // получатель (если отличается от клиента)
 *   "RecipientAddress":   "",
 *   "Analytic":           null,
 *   "DueDate":            "2024-06-30T00:00:00",
 *   "Reference":          "REF-2024-001",
 *   "Currency":           { "ID": 1, "Name": "RSD" },
 *   "Notes":              "",
 *   "DateConfirmed":      null,             // readonly
 *   "DateCompleted":      null,             // readonly
 *   "DateCanceled":       null,             // readonly
 *   "Status":             "O",             // readonly, см. STATUS_*
 *   "DescriptionAbove":   "",
 *   "DescriptionBelow":   "",
 *   "ReportTemplate":     null,            // IN=для выставленных, PN=для полученных
 *   "OrderRows": [
 *     {
 *       "OrderRowId":        1,
 *       "Item":              { "ID": 10, "Name": "Konsultantske usluge" },
 *       "Warehouse":         null,
 *       "ItemName":          "Konsultantske usluge",
 *       "ItemCode":          "U-001",
 *       "Description":       "",
 *       "Quantity":          10.0,
 *       "DiscountPercent":   0.0,
 *       "Price":             5000.00,
 *       "UnitOfMeasurement": "sat",
 *       "RowVersion":        "AAA...="
 *     }
 *   ],
 *   "RowVersion": "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $orders = Yii::$app->minimax->order($orgId);
 *
 * // Создать выставленный заказ
 * $order = $orders->create([
 *     'ReceivedIssued' => MinimaxOrder::TYPE_ISSUED,
 *     'Date'           => '2024-06-01T00:00:00',
 *     'Customer'       => ['ID' => 456],
 *     'CustomerName'   => 'Firma d.o.o.',
 *     'DueDate'        => '2024-06-30T00:00:00',
 *     'OrderRows'      => [
 *         [
 *             'Item'              => ['ID' => 10],
 *             'Quantity'          => 10.0,
 *             'Price'             => 5000.00,
 *             'UnitOfMeasurement' => 'sat',
 *         ],
 *     ],
 * ]);
 *
 * // Подтвердить заказ
 * $orders->confirm($order['OrderId'], $order['RowVersion']);
 *
 * // Создать счёт из подтверждённого заказа
 * $orders->createIssuedInvoice($order['OrderId'], $order['RowVersion']);
 *
 * // Получить PDF заказа и сохранить
 * $orders->savePdf($order['OrderId'], $order['RowVersion'], '/tmp/order.pdf');
 * ```
 */
class MinimaxOrder extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы ReceivedIssued (тип заказа)
    // -----------------------------------------------------------------

    /** Выставленный заказ (клиенту) */
    public const TYPE_ISSUED   = 'I';
    /** Полученный заказ (от поставщика/клиента) */
    public const TYPE_RECEIVED = 'P';

    // -----------------------------------------------------------------
    // Константы Status (readonly)
    // -----------------------------------------------------------------

    /** Черновик */
    public const STATUS_DRAFT       = 'O';
    /** Подтверждён */
    public const STATUS_CONFIRMED   = 'P';
    /** Завершён */
    public const STATUS_COMPLETED   = 'Z';
    /** Аннулирован */
    public const STATUS_INVALIDATED = 'R';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'orders';
    }

    // -----------------------------------------------------------------
    // Стандартные методы
    // -----------------------------------------------------------------

    /**
     * Список заказов с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - ReceivedIssued : тип заказа (TYPE_*)
     *   - Status         : статус (STATUS_*)
     *   - CustomerId     : ID клиента
     *   - DateFrom       : дата от
     *   - DateTo         : дата до
     *   - CurrentPage    : номер страницы (с 1)
     *   - PageSize       : размер страницы
     *   - SortField      : поле сортировки
     *   - Order          : A / D
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
     * Получить PDF заказа (base64).
     *
     * GET api/orgs/{orgId}/orders/{id}/actions/getorderpdf?rowVersion={rowVersion}
     *
     * @throws MinimaxApiException
     */
    public function pdf(int|string $id, string $rowVersion): array
    {
        return $this->getClient()->get(
            $this->buildPath($id, 'actions/getorderpdf'),
            ['rowVersion' => $rowVersion]
        );
    }

    /**
     * Сохранить PDF заказа на диск.
     *
     * @param  int|string $id
     * @param  string     $rowVersion
     * @param  string     $targetPath  Путь для сохранения файла
     * @return string     Путь к сохранённому файлу
     * @throws \RuntimeException если не удалось сохранить
     * @throws MinimaxApiException
     */
    public function savePdf(int|string $id, string $rowVersion, string $targetPath): string
    {
        $result  = $this->pdf($id, $rowVersion);
        $content = base64_decode($result['Data'] ?? $result['Content'] ?? '');

        if ($content === false || $content === '') {
            throw new \RuntimeException(
                "MinimaxOrder: не удалось декодировать PDF заказа {$id}."
            );
        }

        if (file_put_contents($targetPath, $content) === false) {
            throw new \RuntimeException(
                "MinimaxOrder: не удалось записать файл в {$targetPath}."
            );
        }

        return $targetPath;
    }

    // -----------------------------------------------------------------
    // PUT custom actions — управление жизненным циклом
    // -----------------------------------------------------------------

    /**
     * Подтвердить заказ (O → P).
     *
     * @throws MinimaxApiException
     */
    public function confirm(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'confirm', $rowVersion);
    }

    /**
     * Отменить подтверждение заказа (P → O).
     *
     * @throws MinimaxApiException
     */
    public function cancelConfirmation(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'cancelConfirmation', $rowVersion);
    }

    /**
     * Завершить заказ (P → Z).
     *
     * @throws MinimaxApiException
     */
    public function complete(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'complete', $rowVersion);
    }

    /**
     * Отменить завершение заказа (Z → P).
     *
     * @throws MinimaxApiException
     */
    public function cancelCompletion(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'cancelCompletion', $rowVersion);
    }

    /**
     * Аннулировать заказ (P → R).
     *
     * @throws MinimaxApiException
     */
    public function invalidate(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'invalidate', $rowVersion);
    }

    /**
     * Отменить аннулирование заказа (R → P).
     *
     * @throws MinimaxApiException
     */
    public function cancelInvalidation(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'cancelInvalidation', $rowVersion);
    }

    /**
     * Создать счёт (IssuedInvoice) из подтверждённого заказа.
     * Заказ должен быть в статусе P (подтверждён).
     *
     * @return array  Созданный IssuedInvoice
     * @throws MinimaxApiException
     */
    public function createIssuedInvoice(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'createissuedinvoice', $rowVersion);
    }

    /**
     * Сгенерировать PDF (сохранить шаблон отчёта для заказа).
     *
     * @throws MinimaxApiException
     */
    public function generatePdf(int|string $id, string $rowVersion): array
    {
        return $this->putAction($id, 'generatepdf', $rowVersion);
    }

    // -----------------------------------------------------------------
    // Хелперы
    // -----------------------------------------------------------------

    /**
     * Список черновиков заказов.
     *
     * @throws MinimaxApiException
     */
    public function listDrafts(string $type = null, int $pageSize = 50): array
    {
        $params = ['Status' => self::STATUS_DRAFT, 'PageSize' => $pageSize];

        if ($type !== null) {
            $params['ReceivedIssued'] = $type;
        }

        return $this->list($params);
    }

    /**
     * Список подтверждённых заказов (готовых к выставлению счёта).
     *
     * @throws MinimaxApiException
     */
    public function listConfirmed(string $type = null, int $pageSize = 50): array
    {
        $params = ['Status' => self::STATUS_CONFIRMED, 'PageSize' => $pageSize];

        if ($type !== null) {
            $params['ReceivedIssued'] = $type;
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

    // -----------------------------------------------------------------
    // Приватный хелпер для PUT actions
    // -----------------------------------------------------------------

    /**
     * Выполнить PUT custom action.
     * RowVersion передаётся как query-параметр (не в теле).
     */
    private function putAction(int|string $id, string $action, string $rowVersion): array
    {
        return $this->getClient()->put(
            $this->buildPath($id, "actions/{$action}"),
            [],
            ['rowVersion' => $rowVersion]
        );
    }
}
