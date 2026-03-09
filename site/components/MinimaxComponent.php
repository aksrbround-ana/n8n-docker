<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
// use app\components\resources\MinimaxAccount;
// use app\components\resources\MinimaxAnalytic;
// use app\components\resources\MinimaxBankAccount;
// use app\components\resources\MinimaxContact;
use app\components\resources\MinimaxCustomer;
// use app\components\resources\MinimaxDocument;
use app\components\resources\MinimaxEFaktura;
// use app\components\resources\MinimaxEmployee;
// use app\components\resources\MinimaxInbox;
use app\components\resources\MinimaxIssuedInvoice;
// use app\components\resources\MinimaxIssuedInvoicePosting;
// use app\components\resources\MinimaxItem;
// use app\components\resources\MinimaxJournal;
// use app\components\resources\MinimaxOrder;
use app\components\resources\MinimaxOrganisation;
// use app\components\resources\MinimaxOutbox;
// use app\components\resources\MinimaxReceivedInvoice;
// use app\components\resources\MinimaxStock;
// use app\components\resources\MinimaxStockEntry;
// use app\components\resources\MinimaxWarehouse;

/**
 * MinimaxComponent — точка входа для работы с Minimax API.
 *
 * Регистрируется в config/main.php:
 *
 * ```php
 * 'components' => [
 *     'minimax' => [
 *         'class'        => \app\components\MinimaxComponent::class,
 *         'clientId'     => getenv('MINIMAX_CLIENT_ID'),
 *         'clientSecret' => getenv('MINIMAX_CLIENT_SECRET'),
 *         'username'     => getenv('MINIMAX_USERNAME'),
 *         'password'     => getenv('MINIMAX_PASSWORD'),
 *     ],
 * ],
 * ```
 *
 * Использование через фабричные методы:
 *
 * ```php
 * $mm = Yii::$app->minimax;
 *
 * // Ресурсы без organisationId (работают глобально)
 * $orgs = $mm->organisation()->list();
 * $orgId = $orgs[0]['OrganisationId'];
 *
 * // Ресурсы с organisationId
 * $customers = $mm->customer($orgId)->list();
 * $invoice   = $mm->issuedInvoice($orgId)->get(12345);
 * $mm->issuedInvoice($orgId)->send(12345, 'client@example.com');
 *
 * // Сербская специфика
 * $mm->eFaktura($orgId)->sendToSystem(12345);
 * ```
 */
class MinimaxComponent extends Component
{
    // -----------------------------------------------------------------
    // Конфигурация (задаётся через config/main.php из .env)
    // -----------------------------------------------------------------

    /** @var string OAuth2 client_id, выданный Minimax */
    public string $clientId = '';

    /** @var string OAuth2 client_secret, выданный Minimax */
    public string $clientSecret = '';

    /** @var string Email пользователя в Minimax */
    public string $username = '';

    /** @var string Пароль приложения (НЕ пароль аккаунта!),
     *              создаётся в Minimax → Gesla za dostop zunanjih aplikacij */
    public string $password = '';

    /** @var string Базовый URL API. Менять только при необходимости. */
    public string $baseUrl = 'https://moj.minimax.rs/RS';

    /** @var int За сколько секунд до истечения токена обновлять его досрочно */
    public int $tokenRefreshBuffer = 60;

    // -----------------------------------------------------------------
    // Приватное состояние
    // -----------------------------------------------------------------

    private ?MinimaxHttpClient $_client = null;

    /** @var MinimaxResource[] Кэш ресурсов: не создавать одно и то же дважды */
    private array $_resources = [];

    // -----------------------------------------------------------------
    // Yii2 lifecycle
    // -----------------------------------------------------------------

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->validateConfig();
    }

    // -----------------------------------------------------------------
    // Фабричные методы ресурсов
    //
    // Соглашение об именовании: метод = camelCase имя ресурса из API.
    // Ресурсы без organisationId принимают null — они работают глобально.
    // -----------------------------------------------------------------

    public function organisation(): MinimaxOrganisation
    {
        return $this->resource(MinimaxOrganisation::class);
    }

    // public function account(int|string $orgId): MinimaxAccount
    // {
    //     return $this->resource(MinimaxAccount::class, $orgId);
    // }

    // public function analytic(int|string $orgId): MinimaxAnalytic
    // {
    //     return $this->resource(MinimaxAnalytic::class, $orgId);
    // }

    // public function bankAccount(int|string $orgId): MinimaxBankAccount
    // {
    //     return $this->resource(MinimaxBankAccount::class, $orgId);
    // }

    // public function contact(int|string $orgId): MinimaxContact
    // {
    //     return $this->resource(MinimaxContact::class, $orgId);
    // }

    public function customer(int|string $orgId): MinimaxCustomer
    {
        return $this->resource(MinimaxCustomer::class, $orgId);
    }

    // public function document(int|string $orgId): MinimaxDocument
    // {
    //     return $this->resource(MinimaxDocument::class, $orgId);
    // }

    public function eFaktura(int|string $orgId): MinimaxEFaktura
    {
        return $this->resource(MinimaxEFaktura::class, $orgId);
    }

    // public function employee(int|string $orgId): MinimaxEmployee
    // {
    //     return $this->resource(MinimaxEmployee::class, $orgId);
    // }

    // public function inbox(int|string $orgId): MinimaxInbox
    // {
    //     return $this->resource(MinimaxInbox::class, $orgId);
    // }

    public function issuedInvoice(int|string $orgId): MinimaxIssuedInvoice
    {
        return $this->resource(MinimaxIssuedInvoice::class, $orgId);
    }

    // public function issuedInvoicePosting(int|string $orgId): MinimaxIssuedInvoicePosting
    // {
    //     return $this->resource(MinimaxIssuedInvoicePosting::class, $orgId);
    // }

    // public function item(int|string $orgId): MinimaxItem
    // {
    //     return $this->resource(MinimaxItem::class, $orgId);
    // }

    // public function journal(int|string $orgId): MinimaxJournal
    // {
    //     return $this->resource(MinimaxJournal::class, $orgId);
    // }

    // public function order(int|string $orgId): MinimaxOrder
    // {
    //     return $this->resource(MinimaxOrder::class, $orgId);
    // }

    // public function outbox(int|string $orgId): MinimaxOutbox
    // {
    //     return $this->resource(MinimaxOutbox::class, $orgId);
    // }

    // public function receivedInvoice(int|string $orgId): MinimaxReceivedInvoice
    // {
    //     return $this->resource(MinimaxReceivedInvoice::class, $orgId);
    // }

    // public function stock(int|string $orgId): MinimaxStock
    // {
    //     return $this->resource(MinimaxStock::class, $orgId);
    // }

    // public function stockEntry(int|string $orgId): MinimaxStockEntry
    // {
    //     return $this->resource(MinimaxStockEntry::class, $orgId);
    // }

    // public function warehouse(int|string $orgId): MinimaxWarehouse
    // {
    //     return $this->resource(MinimaxWarehouse::class, $orgId);
    // }

    // -----------------------------------------------------------------
    // Прямой доступ к HTTP-клиенту (для нестандартных сценариев)
    // -----------------------------------------------------------------

    public function getClient(): MinimaxHttpClient
    {
        if ($this->_client === null) {
            $this->_client = new MinimaxHttpClient(
                baseUrl:          $this->baseUrl,
                clientId:         $this->clientId,
                clientSecret:     $this->clientSecret,
                username:         $this->username,
                password:         $this->password,
                tokenRefreshBuffer: $this->tokenRefreshBuffer,
            );
        }

        return $this->_client;
    }

    // -----------------------------------------------------------------
    // Вспомогательные методы
    // -----------------------------------------------------------------

    /**
     * Универсальная фабрика ресурсов с кэшированием.
     *
     * Один и тот же класс + organisationId всегда возвращает один объект.
     * Это безопасно: ресурсы stateless, всё состояние хранится в клиенте.
     *
     * @template T of MinimaxResource
     * @param  class-string<T> $class
     * @param  int|string|null $orgId
     * @return T
     */
    private function resource(string $class, int|string|null $orgId = null): MinimaxResource
    {
        $cacheKey = $class . ':' . $orgId;

        if (!isset($this->_resources[$cacheKey])) {
            $this->_resources[$cacheKey] = new $class($this->getClient(), $orgId);
        }

        return $this->_resources[$cacheKey];
    }

    /**
     * Проверяет, что все обязательные параметры заданы.
     *
     * @throws InvalidConfigException
     */
    private function validateConfig(): void
    {
        $required = ['clientId', 'clientSecret', 'username', 'password'];

        foreach ($required as $property) {
            if (empty($this->$property)) {
                throw new InvalidConfigException(
                    static::class . ": параметр «{$property}» обязателен. " .
                    "Проверьте .env и config/main.php."
                );
            }
        }
    }
}