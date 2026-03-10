<?php

namespace app\components\resources;

use app\components\MinimaxHttpClient;
use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxBankAccount — банковские счета клиента (контрагента).
 *
 * ВАЖНО — вложенный ресурс:
 *   BankAccount привязан к конкретному Customer, а не к организации напрямую.
 *   URL всегда содержит customerId:
 *   api/orgs/{organisationId}/customers/{customerId}/bankAccounts/{bankAccountId}
 *
 *   Поэтому класс требует customerId при создании — в отличие от других ресурсов.
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/customers/{customerId}/bankAccounts                    — список
 *   GET    api/orgs/{orgId}/customers/{customerId}/bankAccounts/{id}               — по ID
 *   GET    api/orgs/{orgId}/customers/{customerId}/bankAccounts/synccandidates     — для синхронизации
 *   POST   api/orgs/{orgId}/customers/{customerId}/bankAccounts                    — создать
 *   PUT    api/orgs/{orgId}/customers/{customerId}/bankAccounts/{id}               — обновить
 *   DELETE api/orgs/{orgId}/customers/{customerId}/bankAccounts/{id}               — удалить
 *
 * Структура объекта BankAccount:
 * {
 *   "BankAccountId":    1,
 *   "Customer":         { "ID": 456, "Name": "Firma d.o.o.", "ResourceUrl": "..." },
 *   "Name":             "Račun u Banci Intesa",   // произвольное название
 *   "IBAN":             "RS35105008123123123173",  // IBAN (страна + контрольные цифры)
 *   "AccountNumber":    "105-8123123123173-73",    // Basic Bank Account Number
 *   "BIC":              "DBDBRSBG",               // Bank Identifier Code (SWIFT)
 *   "Default":          "D",                      // D=основной счёт, N=дополнительный
 *   "RecordDtModified": "2024-01-01T00:00:00",
 *   "RowVersion":       "AAA...="
 * }
 *
 * Использование:
 * ```php
 * // Получить банковские счета клиента
 * $ba = Yii::$app->minimax->bankAccount($orgId, customerId: 456);
 * $list = $ba->list();
 *
 * // Или через фабрику с явным customerId
 * $ba = new MinimaxBankAccount($client, $orgId, customerId: 456);
 *
 * // Получить основной счёт клиента
 * $default = $ba->getDefault();
 *
 * // Добавить новый счёт
 * $new = $ba->create([
 *     'Name'          => 'Račun u Raiffeisen banci',
 *     'IBAN'          => 'RS35265008123456789012',
 *     'AccountNumber' => '265-8123456789012-33',
 *     'BIC'           => 'RZBSRSBG',
 *     'Default'       => 'N',
 * ]);
 *
 * // Установить счёт как основной
 * $ba->setDefault($new['BankAccountId']);
 * ```
 */
class MinimaxBankAccount extends MinimaxResource
{
    private int|string $customerId;

    // -----------------------------------------------------------------
    // Конструктор — принимает дополнительный customerId
    // -----------------------------------------------------------------

    public function __construct(
        MinimaxHttpClient $client,
        int|string        $organisationId,
        int|string        $customerId,
    ) {
        $this->customerId = $customerId;
        parent::__construct($client, $organisationId);
    }

    protected function getResourceName(): string
    {
        return 'bankAccounts';
    }

    // -----------------------------------------------------------------
    // Переопределение buildPath — вставляем customerId в URL
    // -----------------------------------------------------------------

    /**
     * Строит вложенный путь:
     * {orgId}/customers/{customerId}/bankAccounts[/{id}[/{suffix}]]
     */
    protected function buildPath(int|string $id = null, string $suffix = null): string
    {
        $parts = [
            $this->getOrganisationId(),
            'customers',
            $this->customerId,
            $this->getResourceName(),
        ];

        if ($id !== null) {
            $parts[] = $id;
        }

        if ($suffix !== null) {
            $parts[] = $suffix;
        }

        return implode('/', $parts);
    }

    // -----------------------------------------------------------------
    // Стандартные методы
    // -----------------------------------------------------------------

    /**
     * Список банковских счетов клиента.
     *
     * Поддерживаемые ключи $params:
     *   - CurrentPage : номер страницы (с 1)
     *   - PageSize    : размер страницы
     *   - SortField   : поле сортировки
     *   - Order       : A / D
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
     * Получить кандидатов для синхронизации.
     *
     * @throws MinimaxApiException
     */
    public function getSyncCandidates(array $params = []): array
    {
        return $this->getClient()->get($this->buildPath('synccandidates'), $params);
    }

    /**
     * Получить основной банковский счёт клиента (Default = 'D').
     * Возвращает первый найденный основной счёт или null если не задан.
     *
     * @throws MinimaxApiException
     */
    public function getDefault(): ?array
    {
        $result = $this->list(['PageSize' => 50]);

        foreach ($result['Rows'] as $row) {
            if (($row['Default'] ?? '') === 'D') {
                return $row;
            }
        }

        return null;
    }

    /**
     * Установить счёт как основной (Default = 'D'),
     * сбросив флаг у всех остальных счетов клиента.
     *
     * Автоматически получает свежий RowVersion перед каждым обновлением.
     *
     * @param  int|string $bankAccountId  ID счёта, который станет основным
     * @return array                      Обновлённый счёт
     * @throws MinimaxApiException
     */
    public function setDefault(int|string $bankAccountId): array
    {
        $result = $this->list(['PageSize' => 50]);

        $updated = null;

        foreach ($result['Rows'] as $row) {
            $id         = $row['BankAccountId'];
            $shouldBeDefault = ($id == $bankAccountId) ? 'D' : 'N';

            // Обновляем только если значение Default изменится
            if (($row['Default'] ?? 'N') !== $shouldBeDefault) {
                $fresh   = $this->get($id);
                $result  = $this->update($id, array_merge($fresh, [
                    'Default' => $shouldBeDefault,
                ]));

                if ($id == $bankAccountId) {
                    $updated = $result;
                }
            }
        }

        // Если счёт уже был основным и не требовал изменений
        if ($updated === null) {
            $updated = $this->get($bankAccountId);
        }

        return $updated;
    }
}