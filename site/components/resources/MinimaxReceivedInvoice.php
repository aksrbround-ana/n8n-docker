<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxReceivedInvoice — входящие счета (от поставщиков) организации.
 *
 * Эндпоинты:
 *   GET    api/orgs/{organisationId}/receivedinvoices                      — список
 *   GET    api/orgs/{organisationId}/receivedinvoices/{id}                 — по ID
 *   POST   api/orgs/{organisationId}/receivedinvoices                      — создать
 *   PUT    api/orgs/{organisationId}/receivedinvoices/{id}                 — обновить
 *   DELETE api/orgs/{organisationId}/receivedinvoices/{id}                 — удалить
 *
 * Отличия от IssuedInvoice:
 *   - Нет метода issue() — входящие счета не «выставляются»
 *   - Нет PDF — документ хранится во Inbox
 *   - Есть DateReceived (дата получения) и DateApproved (дата подтверждения)
 *   - DateApproved обязателен для отчётности в SEF (сербская eFaktura)
 *   - Customer здесь — поставщик (тот, кто выставил счёт нам)
 *
 * Структура объекта ReceivedInvoice (основные поля):
 * {
 *   "ReceivedInvoiceId":     123,                    // readonly при создании
 *   "Year":                  2024,                   // readonly
 *   "InvoiceNumber":         42,                     // readonly
 *   "DocumentNumbering":     { "ID": 1, ... },
 *   "DocumentReference":     "INV-2024-001",         // номер счёта от поставщика
 *   "Customer":              { "ID": 456, ... },     // поставщик
 *   "Employee":              { "ID": 1, ... },       // ответственный сотрудник
 *   "Analytic":              { "ID": 1, ... },       // аналитика
 *   "Currency":              { "ID": 1, "Name": "RSD", ... },
 *   "DateIssued":            "2024-06-01T00:00:00",  // дата счёта от поставщика
 *   "DateTransaction":       "2024-06-01T00:00:00",
 *   "DateDue":               "2024-06-30T00:00:00",
 *   "DateReceived":          "2024-06-03T00:00:00",  // дата получения нами
 *   "DateApproved":          "2024-06-04T00:00:00",  // дата подтверждения (для SEF!)
 *   "ExchangeRate":          1.0,
 *   "Status":                "O",                    // O=черновик, I=проведён (readonly)
 *   "PaymentStatus":         "NeplacanNezapadel",    // readonly, см. константы
 *   "InvoiceValue":          12000.00,               // readonly
 *   "PaidValue":             0.00,                   // readonly
 *   "Notes":                 "",
 *   "ForwardToSEF":          null,                   // Eracun / Zbirno / Posamicno
 *   "ReceivedInvoiceRows": [
 *     {
 *       "RowNumber":         1,
 *       "Description":       "Usluga konsaltinga",
 *       "Quantity":          1.0,
 *       "MU":                "kom",
 *       "Price":             10000.00,
 *       "Discount":          0.0,
 *       "VatRate":           { "ID": 3, ... },
 *       "Value":             10000.00,
 *       "Account":           { "ID": 100, ... }      // бухгалтерский счёт
 *     }
 *   ],
 *   "ReceivedInvoicePaymentMethods": [],
 *   "RowVersion":            "AAA...="               // обязателен при update()
 * }
 *
 * Константы PaymentStatus (readonly, те же что в IssuedInvoice):
 *   Placan                — Оплачен
 *   DelnoPlacanZapadel    — Частично оплачен, просрочен
 *   DelnoPlacanNezapadel  — Частично оплачен, не просрочен
 *   NeplacanZapadel       — Не оплачен, просрочен
 *   NeplacanNezapadel     — Не оплачен, не просрочен
 *   Osnutek               — Черновик
 *   Avans                 — Аванс
 *
 * Использование:
 * ```php
 * $rec = Yii::$app->minimax->receivedInvoice($orgId);
 *
 * // Список с фильтрами
 * $list = $rec->list(['DateIssuedFrom' => '2024-01-01', 'PageSize' => 20]);
 *
 * // Только неоплаченные просроченные
 * $overdue = $rec->listOverdue();
 *
 * // Список от конкретного поставщика
 * $list = $rec->listBySupplier(456);
 *
 * // Создать
 * $draft = $rec->create([
 *     'DocumentNumbering' => ['ID' => 1],
 *     'Customer'          => ['ID' => 456],       // поставщик
 *     'DocumentReference' => 'INV-2024-001',      // номер счёта поставщика
 *     'DateIssued'        => '2024-06-01T00:00:00',
 *     'DateDue'           => '2024-06-30T00:00:00',
 *     'DateReceived'      => '2024-06-03T00:00:00',
 *     'DateApproved'      => '2024-06-04T00:00:00', // обязательно для SEF
 *     'ReceivedInvoiceRows' => [
 *         [
 *             'Description' => 'Usluga konsaltinga',
 *             'Quantity'    => 1.0,
 *             'Price'       => 10000.00,
 *             'VatRate'     => ['ID' => 3],
 *             'Account'     => ['ID' => 100],
 *         ]
 *     ],
 * ]);
 *
 * // Обновить (RowVersion обязателен!)
 * $fresh   = $rec->get(123);
 * $updated = $rec->update(123, array_merge($fresh, [
 *     'DateApproved' => '2024-06-05T00:00:00',
 * ]));
 * ```
 */
class MinimaxReceivedInvoice extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы статусов (зеркало IssuedInvoice для удобства)
    // -----------------------------------------------------------------

    public const STATUS_DRAFT  = 'O';
    public const STATUS_POSTED = 'I';

    public const PAYMENT_PAID                = 'Placan';
    public const PAYMENT_PARTIAL_OVERDUE     = 'DelnoPlacanZapadel';
    public const PAYMENT_PARTIAL_NOT_OVERDUE = 'DelnoPlacanNezapadel';
    public const PAYMENT_UNPAID_OVERDUE      = 'NeplacanZapadel';
    public const PAYMENT_UNPAID_NOT_OVERDUE  = 'NeplacanNezapadel';
    public const PAYMENT_DRAFT               = 'Osnutek';
    public const PAYMENT_ADVANCE             = 'Avans';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'receivedinvoices';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список входящих счетов с фильтрацией и пагинацией.
     *
     * Поддерживаемые ключи $params:
     *   - DateIssuedFrom    : дата счёта поставщика от (YYYY-MM-DD)
     *   - DateIssuedTo      : дата счёта поставщика до (YYYY-MM-DD)
     *   - DateDueFrom       : срок оплаты от
     *   - DateDueTo         : срок оплаты до
     *   - DateReceivedFrom  : дата получения от
     *   - DateReceivedTo    : дата получения до
     *   - Status            : O (черновик) / I (проведён)
     *   - PaymentStatus     : см. константы PAYMENT_*
     *   - CustomerId        : ID поставщика
     *   - SearchString      : поиск по номеру, поставщику, референсу
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
    // Дополнительные методы
    // -----------------------------------------------------------------

    /**
     * Список неоплаченных просроченных входящих счетов.
     *
     * @throws MinimaxApiException
     */
    public function listOverdue(int $pageSize = 50): array
    {
        return $this->list([
            'PaymentStatus' => self::PAYMENT_UNPAID_OVERDUE,
            'PageSize'      => $pageSize,
        ]);
    }

    /**
     * Список входящих счетов от конкретного поставщика за период.
     *
     * @param  int|string  $supplierId  ID поставщика (Customer в терминах Minimax)
     * @param  string|null $dateFrom    Дата от (YYYY-MM-DD)
     * @param  string|null $dateTo      Дата до (YYYY-MM-DD)
     * @throws MinimaxApiException
     */
    public function listBySupplier(
        int|string  $supplierId,
        string|null $dateFrom = null,
        string|null $dateTo   = null,
        int         $pageSize = 50,
    ): array {
        $params = [
            'CustomerId' => $supplierId,
            'PageSize'   => $pageSize,
        ];

        if ($dateFrom !== null) {
            $params['DateIssuedFrom'] = $dateFrom;
        }
        if ($dateTo !== null) {
            $params['DateIssuedTo'] = $dateTo;
        }

        return $this->list($params);
    }

    /**
     * Список счетов, ещё не подтверждённых для SEF (DateApproved не задана).
     * Актуально для сербской eFaktura — без DateApproved счёт нельзя отправить в SEF.
     *
     * @throws MinimaxApiException
     */
    public function listPendingApproval(int $pageSize = 50): array
    {
        return $this->list([
            'Status'   => self::STATUS_DRAFT,
            'PageSize' => $pageSize,
        ]);
    }

    /**
     * Обновить только дату подтверждения счёта (DateApproved).
     * Удобный хелпер для массового подтверждения входящих счетов для SEF.
     *
     * ВАЖНО: автоматически получает свежий RowVersion перед обновлением.
     *
     * @param  int|string $id
     * @param  string     $dateApproved  Дата в формате 'YYYY-MM-DDTHH:MM:SS'
     * @return array      Обновлённый счёт
     * @throws MinimaxApiException
     */
    public function approve(int|string $id, string $dateApproved): array
    {
        $current = $this->get($id);

        return $this->update($id, array_merge($current, [
            'DateApproved' => $dateApproved,
        ]));
    }
}
