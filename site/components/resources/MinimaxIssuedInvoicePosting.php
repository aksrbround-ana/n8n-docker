<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxIssuedInvoicePosting — проводки по выставленным счетам (фискална наплата / ПДВ).
 *
 * Контекст:
 *   IssuedInvoicePosting — это специфический сербский модуль для управления
 *   фискальным учётом розничных продаж и ПДВ-отчётности. Используется для:
 *
 *   - Дневного дохода (IZT): суммарная дневная наплата по кассе
 *   - Дневного дохода с инвойсом (IRI): конкретизация ПДВ, оплат и выручки
 *     в рамках уже зарегистрированного дневного дохода
 *   - Выставленного счёта (IRS): проводка по конкретному выставленному счёту
 *   - Авансовый платёж (AVA): проводка авансового платежа
 *
 *   Важно: не путать с IssuedInvoice — это отдельный документ для фискальной
 *   отчётности, который может быть связан с обычным счётом или быть независимым.
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/issuedinvoicepostings                             — список
 *   GET    api/orgs/{orgId}/issuedinvoicepostings/{id}                        — по ID
 *   GET    api/orgs/{orgId}/issuedinvoicepostings/paymentmethods              — методы оплаты
 *   POST   api/orgs/{orgId}/issuedinvoicepostings                             — создать
 *   PUT    api/orgs/{orgId}/issuedinvoicepostings/{id}/actions/{actionName}   — custom action
 *   DELETE api/orgs/{orgId}/issuedinvoicepostings/{id}                        — удалить
 *
 * Структура объекта IssuedInvoicePosting (сокращённо):
 * {
 *   "IssuedInvoicePostingId":   1,
 *   "DocumentType":             "IZT",           // тип документа, см. TYPE_*
 *   "Status":                   "O",             // O=черновик, P=подтверждён
 *   "DailyIncomeSequentialNumber":               null,
 *   "DailyIncomeInvoicesSequentialNumberFrom":   null,
 *   "DailyIncomeInvoicesSequentialNumberTo":     null,
 *   "DailyIncomeInvoicesCorrections":            null,
 *   "Customer":                 null,            // обязателен для IRI, IRS, AVA
 *   "DateTransaction":          null,            // обязателен для IRS, AVA
 *   "DateDue":                  null,            // обязателен для IRS
 *   "PaymentReference":         null,            // только для IRS
 *   "Analytic":                 null,
 *   "Date":                     "2024-06-01T00:00:00",
 *   "Description":              "Dnevna naknada 01.06.2024",
 *   "Currency":                 { "ID": 1, "Name": "RSD" },
 *   "ExchangeRate":             1.0,
 *   "ForwardToSEF":             "Zbirno",        // для RS: Zbirno / Posamicno
 *   "SalesValue":               50000.00,        // для маzgazina (розница)
 *   "SalesValueVAT":            5000.00,
 *   "PurchaseValue":            30000.00,
 *   "IssuedInvoicePostingPaymentMethods": [...], // методы оплаты (для IZT, IRS)
 *   "IssuedInvoicePostingTaxes":          [...], // налоги (ПДВ)
 *   "IssuedInvoicePostingRevenues":       [...], // выручка по счетам доходов
 *   "IssuedInvoicePostingRetailDataForBookkeeping":              null,
 *   "IssuedInvoicePostingRetailDataForValueBasedStockManagement": null,
 *   "IssuedInvoicePostingRetailDataForStockManagement":          null,
 *   "RowVersion":               "AAA...="
 * }
 *
 * Структура IssuedInvoicePostingTax (ПДВ строка):
 * {
 *   "TaxType":                     "DDV",        // DDV=ПДВ, DavekNaPotrosnjo=акциз, PosebenObracunEU=OSS
 *   "TaxSubjectType":              "B",          // B=товары, S=услуги
 *   "VatRate":                     { "ID": 1, "Name": "20%" },
 *   "TaxPercentage":               20.0,
 *   "TaxBase":                     10000.00,     // база в валюте документа
 *   "TaxAmount":                   2000.00,      // ПДВ в валюте документа
 *   "TaxBaseInDomesticCurrency":   10000.00,
 *   "TaxAmountInDomesticCurrency": 2000.00,
 *   "VatAccountingType":           null,
 *   "TaxExemptionReasonCode":      null          // только для RS
 * }
 *
 * Использование:
 * ```php
 * $posting = Yii::$app->minimax->issuedInvoicePosting($orgId);
 *
 * // Создать проводку дневного дохода
 * $doc = $posting->create([
 *     'DocumentType' => MinimaxIssuedInvoicePosting::TYPE_DAILY_INCOME,
 *     'Date'         => '2024-06-01T00:00:00',
 *     'Description'  => 'Dnevna naknada 01.06.2024',
 *     'ForwardToSEF' => MinimaxIssuedInvoicePosting::SEF_GROUP,
 *     'IssuedInvoicePostingTaxes' => [
 *         [
 *             'TaxType'        => MinimaxIssuedInvoicePosting::TAX_VAT,
 *             'TaxSubjectType' => MinimaxIssuedInvoicePosting::TAX_SUBJECT_SERVICES,
 *             'VatRate'        => ['ID' => 1],
 *             'TaxPercentage'  => 20.0,
 *             'TaxBase'        => 10000.00,
 *             'TaxAmount'      => 2000.00,
 *         ],
 *     ],
 *     'IssuedInvoicePostingPaymentMethods' => [
 *         ['PaymentMethod' => ['ID' => 1], 'Amount' => 12000.00],
 *     ],
 * ]);
 *
 * // Подтвердить (выпустить) проводку
 * $posting->issue($doc['IssuedInvoicePostingId']);
 *
 * // Сторнировать (отменить выпуск)
 * $posting->issueCancellation($doc['IssuedInvoicePostingId']);
 *
 * // Получить доступные методы оплаты для организации
 * $methods = $posting->getPaymentMethods();
 * ```
 */
class MinimaxIssuedInvoicePosting extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы DocumentType
    // -----------------------------------------------------------------

    /** Дневной доход (суммарная наплата по кассе) */
    public const TYPE_DAILY_INCOME         = 'IZT';
    /** Дневной доход с инвойсом (детализация ПДВ/оплат уже зарегистрированного IZT) */
    public const TYPE_DAILY_INCOME_INVOICE = 'IRI';
    /** Выставленный счёт */
    public const TYPE_ISSUED_INVOICE       = 'IRS';
    /** Авансовый платёж */
    public const TYPE_ADVANCE_PAYMENT      = 'AVA';

    // -----------------------------------------------------------------
    // Константы Status
    // -----------------------------------------------------------------

    /** Черновик */
    public const STATUS_DRAFT     = 'O';
    /** Подтверждён / выпущен */
    public const STATUS_CONFIRMED = 'P';

    // -----------------------------------------------------------------
    // Константы ForwardToSEF (пересылка данных в SEF)
    // -----------------------------------------------------------------

    /** Групповая пересылка */
    public const SEF_GROUP      = 'Zbirno';
    /** Индивидуальная пересылка */
    public const SEF_INDIVIDUAL = 'Posamicno';

    // -----------------------------------------------------------------
    // Константы TaxType (тип налога в IssuedInvoicePostingTax)
    // -----------------------------------------------------------------

    /** ПДВ (porez na dodatu vrednost) */
    public const TAX_VAT              = 'DDV';
    /** Акциз (porez na potrošnju) */
    public const TAX_CONSUMPTION      = 'DavekNaPotrosnjo';
    /** Специальный ПДВ для EU One Stop Shop */
    public const TAX_EU_OSS           = 'PosebenObracunEU';

    // -----------------------------------------------------------------
    // Константы TaxSubjectType
    // -----------------------------------------------------------------

    /** Товары */
    public const TAX_SUBJECT_GOODS    = 'B';
    /** Услуги */
    public const TAX_SUBJECT_SERVICES = 'S';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'issuedinvoicepostings';
    }

    // -----------------------------------------------------------------
    // Стандартные методы
    // -----------------------------------------------------------------

    /**
     * Список проводок с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - DocumentType  : тип документа (TYPE_*)
     *   - Status        : статус (STATUS_*)
     *   - DateFrom      : дата документа от
     *   - DateTo        : дата документа до
     *   - CurrentPage   : номер страницы (с 1)
     *   - PageSize      : размер страницы
     *   - SortField     : поле сортировки
     *   - Order         : A / D
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
    // Custom actions
    // -----------------------------------------------------------------

    /**
     * Выпустить (подтвердить) проводку — перевести из черновика в Status=P.
     *
     * PUT api/orgs/{orgId}/issuedinvoicepostings/{id}/actions/issue
     *
     * @throws MinimaxApiException
     */
    public function issue(int|string $id): array
    {
        return $this->getClient()->put(
            $this->buildPath($id, 'actions/issue'),
            []
        );
    }

    /**
     * Сторнировать проводку — отменить выпуск (вернуть в черновик или создать сторно).
     *
     * PUT api/orgs/{orgId}/issuedinvoicepostings/{id}/actions/issueCancellation
     *
     * @throws MinimaxApiException
     */
    public function issueCancellation(int|string $id): array
    {
        return $this->getClient()->put(
            $this->buildPath($id, 'actions/issueCancellation'),
            []
        );
    }

    // -----------------------------------------------------------------
    // Дополнительные методы
    // -----------------------------------------------------------------

    /**
     * Получить доступные методы оплаты для организации.
     * Используется для заполнения IssuedInvoicePostingPaymentMethods.
     *
     * GET api/orgs/{orgId}/issuedinvoicepostings/paymentmethods
     *
     * @return array Список методов оплаты
     * @throws MinimaxApiException
     */
    public function getPaymentMethods(): array
    {
        return $this->getClient()->get($this->buildPath('paymentmethods'));
    }

    /**
     * Список черновиков проводок.
     *
     * @throws MinimaxApiException
     */
    public function listDrafts(int $pageSize = 50): array
    {
        return $this->list([
            'Status'   => self::STATUS_DRAFT,
            'PageSize' => $pageSize,
        ]);
    }

    /**
     * Список проводок дневного дохода за период.
     *
     * @param  string $dateFrom  Формат: 'YYYY-MM-DD'
     * @param  string $dateTo    Формат: 'YYYY-MM-DD'
     * @throws MinimaxApiException
     */
    public function listDailyIncomeByPeriod(
        string $dateFrom,
        string $dateTo,
        int    $pageSize = 50,
    ): array {
        return $this->list([
            'DocumentType' => self::TYPE_DAILY_INCOME,
            'DateFrom'     => $dateFrom,
            'DateTo'       => $dateTo,
            'PageSize'     => $pageSize,
        ]);
    }
}
