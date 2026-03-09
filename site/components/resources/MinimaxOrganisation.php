<?php

namespace app\components\resources;

use app\components\MinimaxHttpClient;
use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxOrganisation — список организаций и детали одной организации.
 *
 * Это особый ресурс: он единственный, кто работает БЕЗ organisationId
 * в URL. Именно отсюда получают OrganisationId для всех остальных запросов.
 *
 * Эндпоинты:
 *   GET api/currentuser/orgs          — список организаций текущего пользователя
 *   GET api/orgs/{organisationId}     — детали одной организации
 *
 * Структура ответа list():
 * {
 *   "Rows": [
 *     {
 *       "Organisation": {
 *         "ID": 123456,
 *         "Name": "Firma d.o.o.",
 *         "ResourceUrl": "https://..."
 *       },
 *       "APIAccess":    "D",   // D = доступ разрешён
 *       "MobileAccess": "D"
 *     }
 *   ],
 *   "TotalRows":         1,
 *   "CurrentPageNumber": 1,
 *   "PageSize":          20
 * }
 *
 * Структура ответа get($id):
 * {
 *   "OrganisationId":        123456,
 *   "Title":                 "Firma d.o.o.",
 *   "Address":               "Ulica 1",
 *   "PostalCode":            "11000",
 *   "City":                  "Beograd",
 *   "Country":               { "ID": 688, "Name": "Srbija", "ResourceUrl": "..." },
 *   "TaxNumber":             "12345678",
 *   "RegistrationNumber":    "12345678",
 *   "VATIdentificationNumber": "RS12345678",
 *   "Administrator":         { "ID": 1, "Name": "Admin", "ResourceUrl": "..." },
 *   "Status":                "V",   // V = активна, B = удалена
 *   "RecordDtModified":      "2024-01-01T00:00:00",
 *   "RowVersion":            "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $mm = Yii::$app->minimax;
 *
 * // Получить все организации
 * $result = $mm->organisation()->list();
 * $orgId  = $result['Rows'][0]['Organisation']['ID'];
 *
 * // Или через удобный хелпер — сразу массив [id => name]
 * $map = $mm->organisation()->getIdNameMap();
 * // [ 123456 => 'Firma d.o.o.', 234567 => 'Druga firma' ]
 *
 * // Получить первую доступную организацию
 * $orgId = $mm->organisation()->getFirstId();
 *
 * // Детали конкретной организации
 * $details = $mm->organisation()->get(123456);
 * ```
 */
class MinimaxOrganisation extends MinimaxResource
{
    // -----------------------------------------------------------------
    // MinimaxOrganisation использует нестандартные URL-пути,
    // поэтому переопределяем все методы базового класса.
    // $resourceName не используется, но обязан быть объявлен.
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'orgs';
    }

    // -----------------------------------------------------------------
    // Публичные методы
    // -----------------------------------------------------------------

    /**
     * Список всех организаций текущего пользователя.
     *
     * Возвращает SearchResult с пагинацией.
     * Для большинства задач достаточно ['Rows'][0...n].
     *
     * Поддерживаемые ключи $params:
     *   - SearchString : строка поиска по названию
     *   - CurrentPage  : номер страницы (с 1), по умолчанию 1
     *   - PageSize     : размер страницы, по умолчанию 50
     *   - SortField    : поле сортировки
     *   - Order        : направление сортировки (A / D)
     *
     * @param  array $params Фильтры и пагинация
     * @return array SearchResult
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 50], $params);

        return $this->getClient()->get('api/currentuser/orgs', $params);
    }

    /**
     * Удобный хелпер для поиска организаций по названию.
     *
     * ```php
     * $mm->organisation()->search('Firma', ['PageSize' => 1]0);
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
     * Детали одной организации по её ID.
     *
     * @param  int|string $id OrganisationId
     * @return array      Organisation
     * @throws MinimaxApiException
     */
    public function get(int|string $id): array
    {
        return $this->getClient()->get("api/orgs/{$id}");
    }

    /**
     * Возвращает ID первой доступной организации.
     * Удобно когда у агентства одна организация.
     *
     * @throws MinimaxApiException
     * @throws \RuntimeException если организаций нет
     */
    public function getFirstId(): int|string
    {
        $result = $this->list(['PageSize' => 1]);

        if (empty($result['Rows'])) {
            throw new \RuntimeException(
                'Minimax: у текущего пользователя нет доступных организаций.'
            );
        }

        return $result['Rows'][0]['Organisation']['ID'];
    }

    /**
     * Возвращает карту [OrganisationId => Name] для всех организаций.
     * Удобно для select-списков в интерфейсе.
     *
     * @throws MinimaxApiException
     */
    public function getIdNameMap(): array
    {
        $result = $this->list(['PageSize' => 100]);
        $map    = [];

        foreach ($result['Rows'] as $row) {
            $org       = $row['Organisation'];
            $map[$org['ID']] = $org['Name'];
        }

        return $map;
    }

    // -----------------------------------------------------------------
    // Заблокированные методы базового класса
    //
    // Organisation — read-only ресурс через API.
    // Организации создаются и удаляются только через интерфейс Minimax.
    // -----------------------------------------------------------------

    public function create(array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxOrganisation: создание организаций через API не поддерживается.'
        );
    }

    public function update(int|string $id, array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxOrganisation: обновление организаций через API не поддерживается.'
        );
    }

    public function delete(int|string $id): array
    {
        throw new \BadMethodCallException(
            'MinimaxOrganisation: удаление организаций через API не поддерживается.'
        );
    }
}