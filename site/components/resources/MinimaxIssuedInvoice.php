<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxIssuedInvoice — выставленные счета и проформы организации.
 *
 * Эндпоинты:
 *   GET    api/orgs/{organisationId}/issuedinvoices                        — список
 *   GET    api/orgs/{organisationId}/issuedinvoices/{id}                   — по ID
 *   POST   api/orgs/{organisationId}/issuedinvoices                        — создать
 *   PUT    api/orgs/{organisationId}/issuedinvoices/{id}                   — обновить
 *   DELETE api/orgs/{organisationId}/issuedinvoices/{id}                   — удалить
 *   POST   api/orgs/{organisationId}/issuedinvoices/{id}/issue             — подтвердить (Draft → Issued)
 *   GET    api/orgs/{organisationId}/issuedinvoices/{id}/pdf               — PDF счёта
 *
 * Структура объекта IssuedInvoice (основные поля):
 * {
 *   "IssuedInvoiceId":       123,                   // readonly при создании
 *   "Year":                  2024,                  // readonly
 *   "InvoiceNumber":         42,                    // readonly
 *   "DocumentNumbering":     { "ID": 1, ... },
 *   "Customer":              { "ID": 456, ... },
 *   "DateIssued":            "2024-06-01T00:00:00",
 *   "DateTransaction":       "2024-06-01T00:00:00",
 *   "DateDue":               "2024-06-30T00:00:00",
 *   "Status":                "O",                   // O=Draft, I=Issued (readonly)
 *   "PaymentStatus":         "NeplacanNezapadel",   // readonly, см. константы ниже
 *   "InvoiceType":           "R",                   // R=счёт, P=проформа
 *   "PricesOnInvoice":       "N",                   // D=НДС включён, N=НДС сверху
 *   "Currency":              { "ID": 1, "Name": "RSD", ... },
 *   "InvoiceValue":          12000.00,              // readonly
 *   "PaidValue":             0.00,                  // readonly
 *   "ForwardToSEF":          null,                  // для eFaktura: Eracun/Zbirno/Posamicno
 *   "IssuedInvoiceRows": [                          // строки счёта
 *     {
 *       "RowNumber":         1,
 *       "Item":              { "ID": 789, ... },    // артикул (опционально)
 *       "Description":       "Usluga konsaltinga",
 *       "Quantity":          1.0,
 *       "MU":                "kom",
 *       "Price":             10000.00,
 *       "Discount":          0.0,
 *       "VatRate":           { "ID": 3, ... },      // ставка НДС
 *       "Value":             10000.00,
 *       "AdditionalWarehouse": null
 *     }
 *   ],
 *   "IssuedInvoicePaymentMethods": [],
 *   "RowVersion":            "AAA...="              // обязателен при update()
 * }
 *
 * Константы PaymentStatus (readonly):
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
 * $inv = Yii::$app->minimax->issuedInvoice($orgId);
 *
 * // Список с фильтрами
 * $list = $inv->list(['DateIssuedFrom' => '2024-01-01', 'PageSize' => 20]);
 *
 * // Только неоплаченные просроченные
 * $overdue = $inv->listOverdue();
 *
 * // Создать черновик
 * $draft = $inv->create([
 *     'DocumentNumbering' => ['ID' => 1],
 *     'Customer'          => ['ID' => 456],
 *     'DateIssued'        => '2024-06-01T00:00:00',
 *     'DateDue'           => '2024-06-30T00:00:00',
 *     'InvoiceType'       => 'R',
 *     'PricesOnInvoice'   => 'N',
 *     'IssuedInvoiceRows' => [
 *         [
 *             'Description' => 'Usluga konsaltinga',
 *             'Quantity'    => 1.0,
 *             'Price'       => 10000.00,
 *             'VatRate'     => ['ID' => 3],
 *         ]
 *     ],
 * ]);
 *
 * // Подтвердить (перевести из Draft в Issued)
 * $inv->issue($draft['IssuedInvoiceId']);
 *
 * // Получить PDF
 * $pdf = $inv->pdf($draft['IssuedInvoiceId']);
 * ```
 */
class MinimaxIssuedInvoice extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы статусов для удобства использования в коде
    // -----------------------------------------------------------------

    public const STATUS_DRAFT  = 'O';
    public const STATUS_ISSUED = 'I';

    public const PAYMENT_PAID                    = 'Placan';
    public const PAYMENT_PARTIAL_OVERDUE         = 'DelnoPlacanZapadel';
    public const PAYMENT_PARTIAL_NOT_OVERDUE     = 'DelnoPlacanNezapadel';
    public const PAYMENT_UNPAID_OVERDUE          = 'NeplacanZapadel';
    public const PAYMENT_UNPAID_NOT_OVERDUE      = 'NeplacanNezapadel';
    public const PAYMENT_DRAFT                   = 'Osnutek';
    public const PAYMENT_ADVANCE                 = 'Avans';

    public const TYPE_INVOICE  = 'R';
    public const TYPE_PROFORMA = 'P';

    public const VAT_INCLUDED = 'D';
    public const VAT_ON_TOP   = 'N';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'issuedinvoices';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список счетов с фильтрацией и пагинацией.
     *
     * Поддерживаемые ключи $params:
     *   - DateIssuedFrom    : дата выставления от (YYYY-MM-DD)
     *   - DateIssuedTo      : дата выставления до (YYYY-MM-DD)
     *   - DateDueFrom       : срок оплаты от
     *   - DateDueTo         : срок оплаты до
     *   - InvoiceType       : R (счёт) / P (проформа)
     *   - Status            : O (черновик) / I (выставлен)
     *   - PaymentStatus     : см. константы PAYMENT_*
     *   - CustomerId        : ID клиента
     *   - SearchString      : поиск по номеру, клиенту
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
     * Подтвердить счёт: перевести из статуса Draft (O) в Issued (I).
     * После подтверждения счёт нельзя редактировать.
     *
     * POST api/orgs/{organisationId}/issuedinvoices/{id}/issue
     *
     * @throws MinimaxApiException
     */
    public function issue(int|string $id): array
    {
        return $this->getClient()->post($this->buildPath($id, 'issue'));
    }

    /**
     * Получить PDF счёта.
     * Возвращает массив с полями: FileName, ContentType, Content (base64).
     *
     * GET api/orgs/{organisationId}/issuedinvoices/{id}/pdf
     *
     * @throws MinimaxApiException
     */
    public function pdf(int|string $id): array
    {
        return $this->getClient()->get($this->buildPath($id, 'pdf'));
    }

    /**
     * Сохранить PDF счёта в файл.
     *
     * @param  int|string $id
     * @param  string     $filePath  Путь для сохранения, например: '/tmp/invoice_123.pdf'
     * @return string                Путь к сохранённому файлу
     * @throws MinimaxApiException
     * @throws \RuntimeException если не удалось сохранить файл
     */
    public function savePdf(int|string $id, string $filePath): string
    {
        $data    = $this->pdf($id);
        $content = base64_decode($data['Content'] ?? '');

        if ($content === false || $content === '') {
            throw new \RuntimeException(
                "MinimaxIssuedInvoice: не удалось декодировать PDF для счёта {$id}."
            );
        }

        if (file_put_contents($filePath, $content) === false) {
            throw new \RuntimeException(
                "MinimaxIssuedInvoice: не удалось сохранить PDF в файл {$filePath}."
            );
        }

        return $filePath;
    }

    /**
     * Список неоплаченных просроченных счетов.
     *
     * @throws MinimaxApiException
     */
    public function listOverdue(int $pageSize = 50): array
    {
        return $this->list([
            'PaymentStatus' => self::PAYMENT_UNPAID_OVERDUE,
            'InvoiceType'   => self::TYPE_INVOICE,
            'PageSize'      => $pageSize,
        ]);
    }

    /**
     * Список счетов конкретного клиента за период.
     *
     * @throws MinimaxApiException
     */
    public function listByCustomer(
        int|string  $customerId,
        string|null $dateFrom = null,
        string|null $dateTo   = null,
        int         $pageSize = 50,
    ): array {
        $params = [
            'CustomerId' => $customerId,
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
}