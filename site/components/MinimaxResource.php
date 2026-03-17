<?php

namespace app\components;

use yii\base\BaseObject;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxResource — базовый класс для всех ресурсов Minimax API.
 *
 * Инкапсулирует:
 * - хранение organisationId (обязателен для всех запросов к API)
 * - построение URL вида {organisationId}/{resource}/{id}
 * - стандартные CRUD-операции (list, get, create, update, delete)
 *
 * Каждый класс-потомок обязан задать $resourceName:
 *
 * ```php
 * class MinimaxCustomer extends MinimaxResource
 * {
 *     protected string $resourceName = 'customer';
 * }
 * ```
 *
 * Использование:
 *
 * ```php
 * $client   = Yii::$app->minimax->getClient();
 * $resource = new MinimaxCustomer($client, $organisationId);
 *
 * $list     = $resource->list();
 * $one      = $resource->get(12345);
 * $created  = $resource->create(['Name' => 'Firma d.o.o.', ...]);
 * $updated  = $resource->update(12345, [..., 'RowVersion' => '...']);
 *             $resource->delete(12345);
 * ```
 */
abstract class MinimaxResource extends BaseObject
{
    // -----------------------------------------------------------------
    // Обязательно переопределить в потомке
    // -----------------------------------------------------------------

    /**
     * Имя ресурса в API, например: 'customer', 'issuedInvoice', 'journal'.
     * Используется при построении URL.
     */
    abstract protected function getResourceName(): string;

    // -----------------------------------------------------------------
    // Зависимости
    // -----------------------------------------------------------------

    private MinimaxHttpClient $client;
    private int|string|null   $organisationId;

    // -----------------------------------------------------------------
    // Конструктор
    // -----------------------------------------------------------------

    public function __construct(MinimaxHttpClient $client, int|string|null $organisationId)
    {
        $this->client         = $client;
        $this->organisationId = $organisationId;

        parent::__construct();
    }

    // -----------------------------------------------------------------
    // Стандартные CRUD-операции
    // -----------------------------------------------------------------

    /**
     * Получить список объектов.
     *
     * @param  array $params Фильтры и пагинация, например:
     *                       ['page' => 1, 'pageSize' => 50, 'filterName' => 'value']
     * @return array
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        return $this->client->get($this->buildPath(), $params);
    }

    /**
     * Получить один объект по ID.
     *
     * @param  int|string $id
     * @return array
     * @throws MinimaxApiException
     */
    public function get(int|string $id): array
    {
        return $this->client->get($this->buildPath($id));
    }

    /**
     * Создать новый объект.
     *
     * @param  array $data Поля нового объекта
     * @return array       Созданный объект (включая присвоенный ID и RowVersion)
     * @throws MinimaxApiException
     */
    public function create(array $data): array
    {
        return $this->client->post($this->buildPath(), $data);
    }

    /**
     * Обновить существующий объект.
     *
     * ВАЖНО: $data должна содержать актуальный RowVersion, полученный
     * при последнем get(). Без него API вернёт ошибку конкурентности.
     *
     * @param  int|string $id
     * @param  array      $data Обновлённые поля + RowVersion
     * @return array            Обновлённый объект
     * @throws MinimaxApiException
     */
    public function update(int|string $id, array $data): array
    {
        return $this->client->put($this->buildPath($id), $data);
    }

    /**
     * Удалить объект по ID.
     *
     * @param  int|string $id
     * @return array
     * @throws MinimaxApiException
     */
    public function delete(int|string $id): array
    {
        return $this->client->delete($this->buildPath($id));
    }

    // -----------------------------------------------------------------
    // Вспомогательные методы для потомков
    // -----------------------------------------------------------------

    /**
     * Строит путь к ресурсу: '{organisationId}/{resourceName}' или
     * '{organisationId}/{resourceName}/{id}'.
     *
     * Потомки могут использовать этот метод для нестандартных URL,
     * например: $this->buildPath($id, 'post') → '123/issuedInvoice/456/post'
     */
    protected function buildPath(int|string|null $id = null, string|null $suffix = null): string
    {
        $parts = [$this->organisationId, $this->getResourceName()];

        if ($id !== null) {
            $parts[] = $id;
        }

        if ($suffix !== null) {
            $parts[] = $suffix;
        }

        return implode('/', $parts);
    }

    /**
     * Прямой доступ к HTTP-клиенту для нестандартных запросов в потомках.
     */
    protected function getClient(): MinimaxHttpClient
    {
        return $this->client;
    }

    /**
     * Прямой доступ к organisationId для нестандартных запросов в потомках.
     */
    protected function getOrganisationId(): int|string
    {
        return $this->organisationId;
    }
}
