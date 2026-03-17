<?php

namespace app\components\resources;

use app\components\MinimaxHttpClient;
use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxContact — контактные лица клиента (контрагента).
 *
 * ВАЖНО — два режима работы:
 * ─────────────────────────────────────────────────────────────────────
 * 1. Режим клиента (customerId задан):
 *    Работа с контактами конкретного клиента.
 *    URL: api/orgs/{orgId}/customers/{customerId}/contacts/...
 *    Доступны: list(), get(), create(), update(), delete(), getSyncCandidates()
 *
 * 2. Глобальный режим (customerId = null):
 *    Получение контактов ВСЕХ клиентов организации.
 *    URL: api/orgs/{orgId}/contacts
 *    Доступны: list() только. get/create/update/delete требуют customerId.
 * ─────────────────────────────────────────────────────────────────────
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/contacts                                          — все контакты
 *   GET    api/orgs/{orgId}/customers/{customerId}/contacts                   — контакты клиента
 *   GET    api/orgs/{orgId}/customers/{customerId}/contacts/{id}              — по ID
 *   GET    api/orgs/{orgId}/customers/{customerId}/contacts/synccandidates    — для синхронизации
 *   POST   api/orgs/{orgId}/customers/{customerId}/contacts                   — создать
 *   PUT    api/orgs/{orgId}/customers/{customerId}/contacts/{id}              — обновить
 *   DELETE api/orgs/{orgId}/customers/{customerId}/contacts/{id}              — удалить
 *
 * Структура объекта Contact:
 * {
 *   "ContactId":        1,
 *   "Customer":         { "ID": 456, "Name": "Firma d.o.o.", "ResourceUrl": "..." },
 *   "FullName":         "Marko Marković",
 *   "PhoneNumber":      "+381 11 123 4567",
 *   "Fax":              "",
 *   "MobilePhone":      "+381 60 123 4567",
 *   "Email":            "marko@firma.rs",
 *   "Notes":            "Direktor",
 *   "Default":          "D",              // D=основной контакт, N=дополнительный
 *   "RecordDtModified": "2024-01-01T00:00:00",
 *   "RowVersion":       "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $mm = Yii::$app->minimax;
 *
 * // Контакты конкретного клиента
 * $contacts = $mm->contact($orgId, customerId: 456);
 * $list     = $contacts->list();
 * $default  = $contacts->getDefault();
 *
 * // Все контакты организации (глобальный режим)
 * $allContacts = $mm->contact($orgId);
 * $list        = $allContacts->list(['SearchString' => 'Marko']);
 *
 * // Создать контакт (только в режиме клиента)
 * $new = $contacts->create([
 *     'FullName'    => 'Ana Anić',
 *     'Email'       => 'ana@firma.rs',
 *     'MobilePhone' => '+381 60 987 6543',
 *     'Default'     => 'N',
 * ]);
 * ```
 */
class MinimaxContact extends MinimaxResource
{
    private int|string|null $customerId;

    // -----------------------------------------------------------------
    // Конструктор — customerId опционален
    // -----------------------------------------------------------------

    public function __construct(
        MinimaxHttpClient  $client,
        int|string         $organisationId,
        int|string|null    $customerId = null,
    ) {
        $this->customerId = $customerId;
        parent::__construct($client, $organisationId);
    }

    protected function getResourceName(): string
    {
        return 'contacts';
    }

    // -----------------------------------------------------------------
    // Построение URL — зависит от режима работы
    // -----------------------------------------------------------------

    /**
     * В режиме клиента:  {orgId}/customers/{customerId}/contacts[/{id}[/{suffix}]]
     * В глобальном режиме: {orgId}/contacts
     */
    protected function buildPath(int|string|null $id = null, string|null $suffix = null): string
    {
        if ($this->customerId === null) {
            // Глобальный режим — только список без id/suffix
            return $this->getOrganisationId() . '/contacts';
        }

        $parts = [
            $this->getOrganisationId(),
            'customers',
            $this->customerId,
            'contacts',
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
     * Список контактов.
     *
     * В режиме клиента — контакты конкретного клиента.
     * В глобальном режиме — контакты всех клиентов организации.
     *
     * Поддерживаемые ключи $params:
     *   - SearchString : поиск по имени, email, телефону
     *   - CurrentPage  : номер страницы (с 1)
     *   - PageSize     : размер страницы
     *   - SortField    : поле сортировки ('FullName', 'Email')
     *   - Order        : A / D
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

    /**
     * Получить контакт по ID.
     * Требует режима клиента (customerId задан).
     *
     * @throws \BadMethodCallException если customerId не задан
     * @throws MinimaxApiException
     */
    public function get(int|string $id): array
    {
        $this->requireCustomerId('get');

        return $this->getClient()->get($this->buildPath($id));
    }

    /**
     * Создать контакт.
     * Требует режима клиента (customerId задан).
     *
     * @throws \BadMethodCallException если customerId не задан
     * @throws MinimaxApiException
     */
    public function create(array $data): array
    {
        $this->requireCustomerId('create');

        return $this->getClient()->post($this->buildPath(), $data);
    }

    /**
     * Обновить контакт.
     * Требует режима клиента (customerId задан).
     *
     * @throws \BadMethodCallException если customerId не задан
     * @throws MinimaxApiException
     */
    public function update(int|string $id, array $data): array
    {
        $this->requireCustomerId('update');

        return $this->getClient()->put($this->buildPath($id), $data);
    }

    /**
     * Удалить контакт.
     * Требует режима клиента (customerId задан).
     *
     * @throws \BadMethodCallException если customerId не задан
     * @throws MinimaxApiException
     */
    public function delete(int|string $id): array
    {
        $this->requireCustomerId('delete');

        return $this->getClient()->delete($this->buildPath($id));
    }

    // -----------------------------------------------------------------
    // Дополнительные методы
    // -----------------------------------------------------------------

    /**
     * Получить кандидатов для синхронизации.
     * Требует режима клиента (customerId задан).
     *
     * @throws \BadMethodCallException если customerId не задан
     * @throws MinimaxApiException
     */
    public function getSyncCandidates(array $params = []): array
    {
        $this->requireCustomerId('getSyncCandidates');

        return $this->getClient()->get($this->buildPath('synccandidates'), $params);
    }

    /**
     * Получить основной контакт клиента (Default = 'D').
     * Требует режима клиента (customerId задан).
     *
     * @return array|null  Контакт или null если не задан
     * @throws \BadMethodCallException если customerId не задан
     * @throws MinimaxApiException
     */
    public function getDefault(): ?array
    {
        $this->requireCustomerId('getDefault');

        $result = $this->list(['PageSize' => 50]);

        foreach ($result['Rows'] as $row) {
            if (($row['Default'] ?? '') === 'D') {
                return $row;
            }
        }

        return null;
    }

    /**
     * Поиск контактов по email (глобальный или в рамках клиента).
     *
     * @throws MinimaxApiException
     */
    public function findByEmail(string $email): array
    {
        $result   = $this->list(['SearchString' => $email, 'PageSize' => 50]);
        $filtered = array_filter(
            $result['Rows'],
            fn(array $row): bool => strtolower($row['Email'] ?? '') === strtolower($email)
        );

        return array_values($filtered);
    }

    // -----------------------------------------------------------------
    // Вспомогательные методы
    // -----------------------------------------------------------------

    private function requireCustomerId(string $method): void
    {
        if ($this->customerId === null) {
            throw new \BadMethodCallException(
                "MinimaxContact::{$method}() требует customerId. " .
                    "Используйте: \$mm->contact(\$orgId, customerId: \$id)"
            );
        }
    }
}
