<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxEmployee — сотрудники организации.
 *
 * Контекст:
 *   Employee используется как ссылка во многих ресурсах:
 *   Journal.JournalEntry.Employee, IssuedInvoice.Employee,
 *   ReceivedInvoice.Employee, Document.Employee.
 *   Для бухгалтерского агентства — это сотрудники клиентских компаний.
 *
 * Эндпоинты:
 *   GET    api/orgs/{organisationId}/employees                        — список
 *   GET    api/orgs/{organisationId}/employees/{id}                   — по ID
 *   GET    api/orgs/{organisationId}/employees/synccandidates         — для синхронизации
 *   POST   api/orgs/{organisationId}/employees                        — создать
 *   PUT    api/orgs/{organisationId}/employees/{id}                   — обновить
 *   DELETE api/orgs/{organisationId}/employees/{id}                   — удалить
 *
 * Структура объекта Employee:
 * {
 *   "EmployeeId":                 1,
 *   "Code":                       "E001",
 *   "TaxNumber":                  "12345678",       // ПИБ сотрудника
 *   "FirstName":                  "Marko",
 *   "LastName":                   "Marković",
 *   "Address":                    "Ulica 1",
 *   "PostalCode":                 "11000",
 *   "City":                       "Beograd",
 *   "Country":                    { "ID": 688, "Name": "Srbija", ... },
 *   "CountryOfResidence":         { "ID": 688, "Name": "Srbija", ... },
 *   "DateOfBirth":                "1985-05-15T00:00:00",
 *   "Gender":                     "M",              // M=мужчина, Z=женщина
 *   "EmploymentStartDate":        "2020-01-01T00:00:00",
 *   "EmploymentEndDate":          null,             // null = работает сейчас
 *   "Notes":                      "",
 *   "EmploymentType":             "ZD",             // тип занятости, см. константы
 *   "PersonalIdenficationNumber": "8505151234567",  // JMBG (13 цифр)
 *   "InsuranceBasis":             null,
 *   "RecordDtModified":           "2024-01-01T00:00:00",
 *   "RowVersion":                 "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $emp = Yii::$app->minimax->employee($orgId);
 *
 * // Список всех активных сотрудников
 * $active = $emp->listActive();
 *
 * // Поиск по имени или фамилии
 * $list = $emp->list(['SearchString' => 'Marko']);
 *
 * // Карта [EmployeeId => FullName] для select-списков
 * $map = $emp->getIdNameMap();
 *
 * // Создать сотрудника
 * $new = $emp->create([
 *     'Code'                       => 'E002',
 *     'FirstName'                  => 'Ana',
 *     'LastName'                   => 'Anić',
 *     'TaxNumber'                  => '87654321',
 *     'PersonalIdenficationNumber' => '9001011234567',
 *     'EmploymentStartDate'        => '2024-01-01T00:00:00',
 *     'EmploymentType'             => MinimaxEmployee::EMPLOYMENT_WORKER,
 *     'Gender'                     => MinimaxEmployee::GENDER_FEMALE,
 *     'Country'                    => ['ID' => 688],
 *     'CountryOfResidence'         => ['ID' => 688],
 * ]);
 *
 * // Завершить трудовые отношения
 * $emp->terminate(1, '2024-12-31T00:00:00');
 * ```
 */
class MinimaxEmployee extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы Gender
    // -----------------------------------------------------------------

    public const GENDER_MALE   = 'M';
    public const GENDER_FEMALE = 'Z';

    // -----------------------------------------------------------------
    // Константы EmploymentType (тип занятости)
    // -----------------------------------------------------------------

    /** Штатный работник */
    public const EMPLOYMENT_WORKER              = 'ZD';
    /** Работающий владелец */
    public const EMPLOYMENT_OWNER              = 'ZL';
    /** Работник до 30 лет */
    public const EMPLOYMENT_UNDER_30           = 'Z30';
    /** Работник от 45 до 50 лет */
    public const EMPLOYMENT_AGE_45_50          = 'Z45';
    /** Работник старше 50 лет */
    public const EMPLOYMENT_OVER_50            = 'Z50';
    /** Стажёр до 30 лет */
    public const EMPLOYMENT_TRAINEE_30         = 'P30';
    /** Инвалид */
    public const EMPLOYMENT_DISABLED           = 'I';
    /** Занятый через кооператив */
    public const EMPLOYMENT_COOPERATIVE        = 'ZADR';
    /** Стипендиат */
    public const EMPLOYMENT_SCHOLARSHIP        = 'S';
    /** Пенсионер */
    public const EMPLOYMENT_PENSIONER          = 'PENZ';
    /** Пенсионер-самозанятый */
    public const EMPLOYMENT_RETIRED_SELF       = 'PENZSD';
    /** Работает в другом месте */
    public const EMPLOYMENT_ELSEWHERE          = 'ZAP';
    /** Безработный */
    public const EMPLOYMENT_UNEMPLOYED         = 'NEZAP';
    /** Фермер */
    public const EMPLOYMENT_FARMER             = 'POL';
    /** Самозанятый */
    public const EMPLOYMENT_SELF               = 'SD';
    /** Нерезидент без международного договора */
    public const EMPLOYMENT_NONRESIDENT        = 'NRNIP';
    /** Нерезидент с международным договором */
    public const EMPLOYMENT_NONRESIDENT_INT    = 'NRJEP';
    /** Ученик — обучение через труд */
    public const EMPLOYMENT_APPRENTICE         = 'UcenjeKrozRad';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'employees';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список сотрудников с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - SearchString  : поиск по имени, фамилии, коду, ПИБ
     *   - CurrentPage   : номер страницы (с 1)
     *   - PageSize      : размер страницы
     *   - SortField     : поле сортировки ('LastName', 'FirstName', 'Code')
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
     * Получить кандидатов для синхронизации.
     *
     * @throws MinimaxApiException
     */
    public function getSyncCandidates(array $params = []): array
    {
        return $this->getClient()->get($this->buildPath('synccandidates'), $params);
    }

    /**
     * Список активных сотрудников (EmploymentEndDate = null или в будущем).
     * API не поддерживает прямой фильтрации — фильтруем на клиенте.
     *
     * @throws MinimaxApiException
     */
    public function listActive(int $pageSize = 200): array
    {
        $result = $this->list(['PageSize' => $pageSize]);
        $now    = new \DateTimeImmutable();

        $filtered = array_filter(
            $result['Rows'],
            function (array $row) use ($now): bool {
                if (empty($row['EmploymentEndDate'])) {
                    return true;
                }
                return new \DateTimeImmutable($row['EmploymentEndDate']) >= $now;
            }
        );

        return array_values($filtered);
    }

    /**
     * Карта [EmployeeId => 'FirstName LastName'] для select-списков.
     *
     * @throws MinimaxApiException
     */
    public function getIdNameMap(int $pageSize = 200): array
    {
        $rows = $this->listActive($pageSize);
        $map  = [];

        foreach ($rows as $row) {
            $map[$row['EmployeeId']] = trim($row['FirstName'] . ' ' . $row['LastName']);
        }

        return $map;
    }

    /**
     * Завершить трудовые отношения (задать EmploymentEndDate).
     * Автоматически получает свежий RowVersion перед обновлением.
     *
     * @param  int|string $id
     * @param  string     $endDate  Формат: 'YYYY-MM-DDTHH:MM:SS'
     * @return array      Обновлённый сотрудник
     * @throws MinimaxApiException
     */
    public function terminate(int|string $id, string $endDate): array
    {
        $current = $this->get($id);

        return $this->update($id, array_merge($current, [
            'EmploymentEndDate' => $endDate,
        ]));
    }
}
