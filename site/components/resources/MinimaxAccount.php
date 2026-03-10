<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxAccount — справочник счетов бухгалтерского плана организации.
 *
 * Контекст:
 *   Account (счёт) — это позиция контного плана (контни план), например:
 *   100 — Potraživanja od kupaca (дебиторская задолженность)
 *   200 — Prihodi od usluga (доходы от услуг)
 *   470 — PDV po izlaznim fakturama (НДС по выставленным счетам)
 *   Счета используются в строках Journal (JournalEntry.Account).
 *
 * Эндпоинты:
 *   GET api/orgs/{organisationId}/accounts                             — список
 *   GET api/orgs/{organisationId}/accounts/{id}                       — по ID
 *   GET api/orgs/{organisationId}/accounts/code({code})               — по коду
 *   GET api/orgs/{organisationId}/accounts/content({content})         — по контенту
 *   GET api/orgs/{organisationId}/accounts/synccandidates             — кандидаты синхронизации
 *   POST   api/orgs/{organisationId}/accounts                         — создать
 *   PUT    api/orgs/{organisationId}/accounts/{id}                    — обновить
 *   DELETE api/orgs/{organisationId}/accounts/{id}                    — удалить
 *
 * Структура объекта Account:
 * {
 *   "AccountId":            100,
 *   "Code":                 "1000",           // код по контному плану
 *   "Name":                 "Potraživanja od kupaca",
 *   "NameInOtherLanguage":  "Forderungen",    // на другом языке (опц.)
 *   "NameInEnglish":        "Trade receivables",
 *   "Description":          "",
 *   "AllowedPosting":       "V",             // V=оба, B=только дебет, D=только кредит, N=запрещено
 *   "InvoiceAccounting":    "B",             // N=нет, B=дебет, D=кредит
 *   "AnalyticsEntry":       "N",             // V=разрешено, N=запрещено, D=обязательно
 *   "EmployeeEntry":        "N",             // V=разрешено, N=запрещено, D=обязательно
 *   "CustomerEntry":        "D",             // V=разрешено, N=запрещено, D=обязательно
 *   "NonTaxable":           "N",             // D=да (не облагается), N=нет
 *   "Application":          "D",             // D=активен, N=неактивен
 *   "ValidFromYear":        2020,            // readonly
 *   "ValidToYear":          0,               // readonly, 0 = без ограничений
 *   "RecordDtModified":     "2024-01-01T00:00:00",
 *   "RowVersion":           "AAA...="        // обязателен при update()
 * }
 *
 * AllowedPosting — допустимая сторона проводки:
 *   V — дебет и кредит (обе стороны)
 *   B — только дебет (Bremeniti = нагружать)
 *   D — только кредит (Dobrovati = кредитовать)
 *   N — проводки запрещены (группировочный счёт)
 *
 * CustomerEntry / AnalyticsEntry / EmployeeEntry:
 *   V — ввод разрешён (необязательно)
 *   N — ввод запрещён
 *   D — ввод обязателен (Dovoljno = достаточно / обязательно)
 *   Это влияет на то, какие доп. поля обязательны в JournalEntry.
 *
 * Использование:
 * ```php
 * $accounts = Yii::$app->minimax->account($orgId);
 *
 * // Список всех активных счетов
 * $list = $accounts->list(['Application' => 'D']);
 *
 * // По коду из контного плана
 * $acc = $accounts->getByCode('1000');
 *
 * // Карта [код => ['ID', 'Name']] для выпадающих списков в Journal
 * $map = $accounts->getCodeMap();
 *
 * // Только счета, требующие обязательного ввода клиента (CustomerEntry = 'D')
 * // Полезно при построении UI для Journal
 * $customerRequired = $accounts->listRequiringCustomer();
 *
 * // Кандидаты для синхронизации (изменённые с последней синхронизации)
 * $sync = $accounts->getSyncCandidates();
 * ```
 */
class MinimaxAccount extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы AllowedPosting
    // -----------------------------------------------------------------

    /** Дебет и кредит */
    public const POSTING_BOTH   = 'V';
    /** Только дебет */
    public const POSTING_DEBIT  = 'B';
    /** Только кредит */
    public const POSTING_CREDIT = 'D';
    /** Проводки запрещены */
    public const POSTING_NONE   = 'N';

    // -----------------------------------------------------------------
    // Константы для CustomerEntry / AnalyticsEntry / EmployeeEntry
    // -----------------------------------------------------------------

    /** Ввод разрешён, но необязателен */
    public const ENTRY_ALLOWED    = 'V';
    /** Ввод запрещён */
    public const ENTRY_FORBIDDEN  = 'N';
    /** Ввод обязателен */
    public const ENTRY_MANDATORY  = 'D';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'accounts';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список счетов контного плана с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - SearchString  : поиск по коду или названию счёта
     *   - Application   : D (активные) / N (неактивные)
     *   - CurrentPage   : номер страницы (с 1)
     *   - PageSize      : размер страницы
     *   - SortField     : поле сортировки (например: 'Code', 'Name')
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
    // Дополнительные методы
    // -----------------------------------------------------------------

    /**
     * Найти счёт по коду контного плана (например: '1000', '470').
     *
     * GET api/orgs/{organisationId}/accounts/code({code})
     *
     * @throws MinimaxApiException
     */
    public function getByCode(string $code): array
    {
        return $this->getClient()->get($this->buildPath("code({$code})"));
    }

    /**
     * Найти счёт по контенту (на основе настроек организации).
     * Используется для поиска по названию на языке организации.
     *
     * GET api/orgs/{organisationId}/accounts/content({content})
     *
     * @throws MinimaxApiException
     */
    public function getByContent(string $content): array
    {
        return $this->getClient()->get($this->buildPath("content({$content})"));
    }

    /**
     * Получить кандидатов для синхронизации —
     * счета, изменённые с момента последней синхронизации.
     *
     * GET api/orgs/{organisationId}/accounts/synccandidates
     *
     * @throws MinimaxApiException
     */
    public function getSyncCandidates(array $params = []): array
    {
        return $this->getClient()->get($this->buildPath('synccandidates'), $params);
    }

    /**
     * Карта [Code => ['ID' => id, 'Name' => name]] для всех активных счетов.
     * Удобно при построении строк Journal — можно найти счёт по коду.
     *
     * ```php
     * $map = $accounts->getCodeMap();
     * $accountId = $map['1000']['ID']; // ID счёта 1000
     * ```
     *
     * @throws MinimaxApiException
     */
    public function getCodeMap(int $pageSize = 500): array
    {
        $result = $this->list(['Application' => 'D', 'PageSize' => $pageSize]);
        $map    = [];

        foreach ($result['Rows'] as $row) {
            $map[$row['Code']] = [
                'ID'   => $row['AccountId'],
                'Name' => $row['Name'],
            ];
        }

        return $map;
    }

    /**
     * Список счетов, для которых обязателен ввод клиента (CustomerEntry = 'D').
     * Полезно при валидации строк Journal перед отправкой.
     *
     * @throws MinimaxApiException
     */
    public function listRequiringCustomer(int $pageSize = 200): array
    {
        // API не поддерживает прямую фильтрацию по CustomerEntry,
        // поэтому фильтруем на стороне клиента
        $result   = $this->list(['Application' => 'D', 'PageSize' => $pageSize]);
        $filtered = array_filter(
            $result['Rows'],
            fn($row) => ($row['CustomerEntry'] ?? '') === self::ENTRY_MANDATORY
        );

        return array_values($filtered);
    }
}
