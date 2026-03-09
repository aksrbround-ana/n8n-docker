<?php

namespace app\components\resources;

use app\components\MinimaxHttpClient;
use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxCustomer — клиенты / контрагенты организации.
 *
 * Эндпоинты:
 *   GET    api/orgs/{organisationId}/customers                     — список
 *   GET    api/orgs/{organisationId}/customers/{id}                — по ID
 *   GET    api/orgs/{organisationId}/customers/code({code})        — по коду
 *   POST   api/orgs/{organisationId}/customers                     — создать
 *   PUT    api/orgs/{organisationId}/customers/{id}                — обновить
 *   DELETE api/orgs/{organisationId}/customers/{id}                — удалить
 *
 * Структура объекта Customer:
 * {
 *   "CustomerId":                 123,          // только чтение при создании
 *   "Code":                       "K001",        // уникален в рамках организации
 *   "Name":                       "Firma d.o.o.",
 *   "Address":                    "Ulica 1",
 *   "PostalCode":                 "11000",
 *   "City":                       "Beograd",
 *   "Country":                    { "ID": 688, "Name": "Srbija", "ResourceUrl": "..." },
 *   "CountryName":                "Srbija",
 *   "TaxNumber":                  "12345678",
 *   "RegistrationNumber":         "12345678",
 *   "VATIdentificationNumber":    "RS12345678",
 *   "SubjectToVAT":               "D",          // D / M / N — см. ниже
 *   "ConsiderCountryForBookkeeping": "N",        // D / N
 *   "Currency":                   { "ID": 1, "Name": "RSD", "ResourceUrl": "..." },
 *   "ExpirationDays":             30,
 *   "RebatePercent":              0.00,
 *   "WebSiteURL":                 "https://firma.rs",
 *   "EInvoiceIssuing":            "SeNePripravlja", // SeNePripravlja / Ponudnik / EPosta
 *   "InternalCustomerNumber":     "",
 *   "GLN":                        "",
 *   "BudgetUserNumber":           "",
 *   "Usage":                      "D",          // D = активен, N = неактивен
 *   "AssociationType":            "Ostala",     // Maticna / Odvisna / Ostala
 *   "RecordDtModified":           "2024-01-01T00:00:00",
 *   "RowVersion":                 "AAA...="     // обязателен при update()
 * }
 *
 * SubjectToVAT — статус НДС:
 *   Для клиентов внутри ЕС:
 *     D — юр. лицо / ИП, плательщик НДС
 *     M — юр. лицо / ИП, НЕ плательщик НДС
 *     N — конечный потребитель
 *   Для клиентов вне ЕС:
 *     D — юр. лицо (НДС на счёт не начисляется)
 *     N — конечный потребитель
 *
 * Использование:
 * ```php
 * $customers = Yii::$app->minimax->customer($orgId);
 *
 * // Список с фильтрами
 * $list = $customers->list(['SearchString' => 'Firma', 'PageSize' => 20]);
 *
 * // Удобный поиск
 * $list = $customers->search('Firma');
 *
 * // По ID
 * $one = $customers->get(123);
 *
 * // По внутреннему коду (уникален в организации)
 * $one = $customers->getByCode('K001');
 *
 * // Создать
 * $new = $customers->create([
 *     'Code'         => 'K002',
 *     'Name'         => 'Nova firma d.o.o.',
 *     'TaxNumber'    => '87654321',
 *     'SubjectToVAT' => 'D',
 *     'Country'      => ['ID' => 688],
 *     'Usage'        => 'D',
 * ]);
 *
 * // Обновить (RowVersion обязателен!)
 * $fresh   = $customers->get(123);
 * $updated = $customers->update(123, array_merge($fresh, ['Name' => 'Novo ime']));
 *
 * // Удалить
 * $customers->delete(123);
 * ```
 */
class MinimaxCustomer extends MinimaxResource
{
    protected function getResourceName(): string
    {
        return 'customers';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список клиентов с фильтрацией и пагинацией.
     *
     * Поддерживаемые ключи $params:
     *   - SearchString  : поиск по названию, коду, ИНН
     *   - CurrentPage   : номер страницы (с 1)
     *   - PageSize      : размер страницы
     *   - SortField     : поле сортировки (например: 'Name', 'Code')
     *   - Order         : A (возр.) / D (убыв.)
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
     * Найти клиента по его внутреннему коду (уникален в организации).
     *
     * GET api/orgs/{organisationId}/customers/code({code})
     *
     * @throws MinimaxApiException
     */
    public function getByCode(string $code): array
    {
        return $this->getClient()->get(
            $this->buildPath("code({$code})")
        );
    }

    /**
     * Удобный поиск по строке.
     *
     * ```php
     * $customers->search('Firma', pageSize: 10);
     * ```
     *
     * @throws MinimaxApiException
     */
    public function search(string $query, int $page = 1, int $pageSize = 50): array
    {
        return $this->list([
            'SearchString' => $query,
            'CurrentPage'  => $page,
            'PageSize'     => $pageSize,
        ]);
    }

    /**
     * Возвращает карту [CustomerId => Name] для всех клиентов.
     * Удобно для select-списков в интерфейсе.
     * При большом количестве клиентов увеличьте $pageSize.
     *
     * @throws MinimaxApiException
     */
    public function getIdNameMap(int $pageSize = 200): array
    {
        $result = $this->list(['PageSize' => $pageSize]);
        $map    = [];

        foreach ($result['Rows'] as $row) {
            $map[$row['CustomerId']] = $row['Name'];
        }

        return $map;
    }
}
