<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxJournal — журнал бухгалтерских проводок (налог за књижење).
 *
 * Эндпоинты:
 *   GET    api/orgs/{organisationId}/journals                              — список журналов
 *   GET    api/orgs/{organisationId}/journals/{id}                        — один журнал со строками
 *   POST   api/orgs/{organisationId}/journals                             — создать журнал
 *   PUT    api/orgs/{organisationId}/journals/{id}                        — обновить журнал
 *   DELETE api/orgs/{organisationId}/journals/{id}                        — удалить журнал
 *   GET    api/orgs/{organisationId}/journals/{id}/entries                 — только строки журнала
 *   GET    api/orgs/{organisationId}/journals/{id}/vat/{vatId}            — запись НДС журнала
 *
 * Структура объекта Journal:
 * {
 *   "JournalId":     123,
 *   "JournalType":   { "ID": 2, "Name": "Temeljnica", "ResourceUrl": "..." },
 *   "JournalDate":   "2024-06-01T00:00:00",
 *   "Description":   "IR:2024-042",
 *   "Status":        "O",                 // O=черновик, I=проведён (readonly)
 *   "JournalEntries": [
 *     {
 *       "ExternalId":               "terjatev",   // внешний ID для связи с НДС-записями
 *       "JournalEntryId":           456,
 *       "JournalEntryDate":         "2024-06-01T00:00:00",
 *       "DueDate":                  "2024-06-30T00:00:00",
 *       "TransactionDate":          "2024-06-01T00:00:00",
 *       "Account":                  { "ID": 100, "Name": "Potraživanja od kupaca", ... },
 *       "Customer":                 { "ID": 456, ... },
 *       "Analytic":                 null,
 *       "Employee":                 null,
 *       "Currency":                 { "ID": 1, "Name": "RSD", ... },
 *       "Description":              "Faktura br. 42",
 *       "PaymentReference":         "20240042",
 *       "Debit":                    12000.00,
 *       "Credit":                   0.00,
 *       "DebitInDomesticCurrency":  12000.00,
 *       "CreditInDomesticCurrency": 0.00,
 *       "VatBase":                  null,         // для OSS отчётности
 *       "RowVersion":               "AAA...="
 *     },
 *     {
 *       "ExternalId":               null,
 *       "JournalEntryDate":         "2024-06-01T00:00:00",
 *       "Account":                  { "ID": 200, "Name": "Prihodi od usluga", ... },
 *       "Debit":                    0.00,
 *       "Credit":                   10000.00,
 *       "DebitInDomesticCurrency":  0.00,
 *       "CreditInDomesticCurrency": 10000.00
 *     },
 *     {
 *       "Account":                  { "ID": 470, "Name": "PDV po izlaznim fakturama", ... },
 *       "Debit":                    0.00,
 *       "Credit":                   2000.00,
 *       "DebitInDomesticCurrency":  0.00,
 *       "CreditInDomesticCurrency": 2000.00
 *     }
 *   ],
 *   "RecordDtModified": "2024-06-01T00:00:00",
 *   "RowVersion":       "AAA...="
 * }
 *
 * ВАЖНО — правило двойной записи:
 *   Сумма всех Debit == Сумма всех Credit (как в RSD, так и в валюте).
 *   Если суммы не совпадают, API вернёт ошибку валидации.
 *
 * ExternalId:
 *   Используется для связи строк журнала с НДС-записями (VATEntry).
 *   Строка с ExternalId может быть связана с VATEntry через поле
 *   JournalEntryExternalId. Это ключевой механизм для корректной
 *   отчётности по НДС в сербской eFaktura/SEF.
 *
 * Использование:
 * ```php
 * $journal = Yii::$app->minimax->journal($orgId);
 *
 * // Список журналов за период
 * $list = $journal->list([
 *     'DateFrom' => '2024-01-01',
 *     'DateTo'   => '2024-06-30',
 *     'PageSize' => 100,
 * ]);
 *
 * // Только черновики
 * $drafts = $journal->listDrafts();
 *
 * // Создать журнал с проводками (пример: выставленный счёт)
 * $new = $journal->create([
 *     'JournalType' => ['ID' => 2],
 *     'JournalDate' => '2024-06-01T00:00:00',
 *     'Description' => 'IR:2024-042',
 *     'JournalEntries' => [
 *         // Дебет: дебиторская задолженность
 *         [
 *             'ExternalId'        => 'terjatev',
 *             'JournalEntryDate'  => '2024-06-01T00:00:00',
 *             'DueDate'           => '2024-06-30T00:00:00',
 *             'TransactionDate'   => '2024-06-01T00:00:00',
 *             'Account'           => ['ID' => 100],
 *             'Customer'          => ['ID' => 456],
 *             'Currency'          => ['ID' => 1],
 *             'PaymentReference'  => '20240042',
 *             'Debit'             => 12000.00,
 *             'Credit'            => 0.00,
 *             'DebitInDomesticCurrency'  => 12000.00,
 *             'CreditInDomesticCurrency' => 0.00,
 *         ],
 *         // Кредит: доходы
 *         [
 *             'JournalEntryDate'  => '2024-06-01T00:00:00',
 *             'Account'           => ['ID' => 200],
 *             'Currency'          => ['ID' => 1],
 *             'Debit'             => 0.00,
 *             'Credit'            => 10000.00,
 *             'DebitInDomesticCurrency'  => 0.00,
 *             'CreditInDomesticCurrency' => 10000.00,
 *         ],
 *         // Кредит: НДС
 *         [
 *             'JournalEntryDate'  => '2024-06-01T00:00:00',
 *             'Account'           => ['ID' => 470],
 *             'Currency'          => ['ID' => 1],
 *             'Debit'             => 0.00,
 *             'Credit'            => 2000.00,
 *             'DebitInDomesticCurrency'  => 0.00,
 *             'CreditInDomesticCurrency' => 2000.00,
 *         ],
 *     ],
 * ]);
 *
 * // Получить только строки журнала (без заголовка)
 * $entries = $journal->getEntries(123);
 *
 * // Проверить баланс перед отправкой
 * $journal->assertBalanced($entries);
 * ```
 */
class MinimaxJournal extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы статусов
    // -----------------------------------------------------------------

    public const STATUS_DRAFT  = 'O';
    public const STATUS_POSTED = 'I';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'journals';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список журналов с фильтрацией и пагинацией.
     *
     * Поддерживаемые ключи $params:
     *   - DateFrom       : дата журнала от (YYYY-MM-DD)
     *   - DateTo         : дата журнала до (YYYY-MM-DD)
     *   - Status         : O (черновик) / I (проведён)
     *   - JournalTypeId  : ID типа журнала
     *   - SearchString   : поиск по описанию
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
    // Дополнительные методы
    // -----------------------------------------------------------------

    /**
     * Получить только строки (entries) журнала без заголовка.
     * Удобно для экспорта и анализа проводок.
     *
     * GET api/orgs/{organisationId}/journals/{id}/entries
     *
     * @throws MinimaxApiException
     */
    public function getEntries(int|string $id, array $params = []): array
    {
        return $this->getClient()->get($this->buildPath($id, 'entries'), $params);
    }

    /**
     * Список журналов-черновиков (ещё не проведённых).
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
     * Список журналов за период.
     *
     * @throws MinimaxApiException
     */
    public function listByPeriod(string $dateFrom, string $dateTo, int $pageSize = 100): array
    {
        return $this->list([
            'DateFrom' => $dateFrom,
            'DateTo'   => $dateTo,
            'PageSize' => $pageSize,
        ]);
    }

    /**
     * Проверить, что журнал сбалансирован (Дебет == Кредит).
     * Вызывайте перед create() чтобы получить понятную ошибку,
     * а не загадочный ответ от API.
     *
     * @param  array $journalEntries  Массив строк из 'JournalEntries'
     * @throws \InvalidArgumentException если журнал не сбалансирован
     */
    public function assertBalanced(array $journalEntries): void
    {
        $totalDebit  = 0.0;
        $totalCredit = 0.0;

        foreach ($journalEntries as $entry) {
            $totalDebit  += (float)($entry['DebitInDomesticCurrency']  ?? $entry['Debit']  ?? 0);
            $totalCredit += (float)($entry['CreditInDomesticCurrency'] ?? $entry['Credit'] ?? 0);
        }

        // Допускаем погрешность округления до 0.01
        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \InvalidArgumentException(sprintf(
                'Журнал не сбалансирован: Дебет=%.2f, Кредит=%.2f, разница=%.2f.',
                $totalDebit,
                $totalCredit,
                $totalDebit - $totalCredit,
            ));
        }
    }

    /**
     * Создать журнал с предварительной проверкой баланса.
     *
     * @throws \InvalidArgumentException если дебет ≠ кредит
     * @throws MinimaxApiException
     */
    public function createBalanced(array $data): array
    {
        $this->assertBalanced($data['JournalEntries'] ?? []);

        return $this->create($data);
    }
}
