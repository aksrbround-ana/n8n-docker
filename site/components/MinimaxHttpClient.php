<?php

namespace app\components;

use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;
use yii\httpclient\Exception as HttpClientException;
use app\components\exceptions\MinimaxAuthException;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxHttpClient — низкоуровневый HTTP-клиент для Minimax API.
 *
 * Отвечает за:
 * - получение и кэширование OAuth2-токена
 * - автоматическое обновление токена по истечении
 * - выполнение GET / POST / PUT / DELETE запросов
 *
 * Не используется напрямую — получается через MinimaxComponent::getClient().
 *
 * Требует расширения yii2-httpclient:
 *   composer require yiisoft/yii2-httpclient
 */
class MinimaxHttpClient extends BaseObject
{
    // -----------------------------------------------------------------
    // Константы
    // -----------------------------------------------------------------

    private const TOKEN_CACHE_KEY = 'minimax_oauth_token';

    // Сербский Minimax: /RS/AUT/OAuth20/Token
    // Словенский Minimax: /SI/AUT/OAuth20/Token
    private const TOKEN_ENDPOINT  = '/AUT/OAuth20/Token';

    // Реальный базовый путь API — содержит удвоенное /api/api/
    // Документация: https://moj.minimax.rs/RS/api/api/currentuser/orgs
    private const API_PREFIX = '/api/';

    // -----------------------------------------------------------------
    // Конфигурация (задаётся из MinimaxComponent)
    // -----------------------------------------------------------------

    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $username;
    private string $password;
    private int    $tokenRefreshBuffer;

    // -----------------------------------------------------------------
    // Конструктор
    // -----------------------------------------------------------------

    public function __construct(
        string $baseUrl,
        string $clientId,
        string $clientSecret,
        string $username,
        string $password,
        int    $tokenRefreshBuffer = 60,
    ) {
        $this->baseUrl            = rtrim($baseUrl, '/');
        $this->clientId           = $clientId;
        $this->clientSecret       = $clientSecret;
        $this->username           = $username;
        $this->password           = $password;
        $this->tokenRefreshBuffer = $tokenRefreshBuffer;

        parent::__construct();
    }

    // -----------------------------------------------------------------
    // Публичные HTTP-методы (используются классами-ресурсами)
    // -----------------------------------------------------------------

    /**
     * GET /RS/API/{organisationId}/{resource}
     *
     * @param  string $path   например: '123456/issuedInvoice'
     * @param  array  $params query string параметры
     * @return array          декодированный JSON-ответ
     * @throws MinimaxApiException
     */
    public function get(string $path, array $params = []): array
    {
        return $this->request('GET', $path, queryParams: $params);
    }

    /**
     * POST /RS/API/{organisationId}/{resource}
     *
     * @param  string $path
     * @param  array  $body  данные для создания объекта
     * @return array
     * @throws MinimaxApiException
     */
    public function post(string $path, array $body = []): array
    {
        return $this->request('POST', $path, body: $body);
    }

    /**
     * PUT /RS/API/{organisationId}/{resource}/{id}
     *
     * ВАЖНО: тело запроса должно содержать актуальный RowVersion,
     * полученный при последнем GET. Иначе API вернёт ошибку конкурентности.
     *
     * @param  string $path
     * @param  array  $body  данные для обновления, включая RowVersion
     * @return array
     * @throws MinimaxApiException
     */
    public function put(string $path, array $body = []): array
    {
        return $this->request('PUT', $path, body: $body);
    }

    /**
     * DELETE /RS/API/{organisationId}/{resource}/{id}
     *
     * @param  string $path
     * @return array
     * @throws MinimaxApiException
     */
    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    // -----------------------------------------------------------------
    // Авторизация и токен
    // -----------------------------------------------------------------

    /**
     * Возвращает валидный access token.
     * Берёт из кэша, если ещё не истёк (с учётом буфера).
     * Запрашивает новый, если истёк или отсутствует.
     *
     * @throws MinimaxAuthException
     */
    public function getAccessToken(): string
    {
        $cached = Yii::$app->cache->get(self::TOKEN_CACHE_KEY);

        if ($cached !== false) {
            return $cached;
        }

        return $this->fetchAndCacheToken();
    }

    /**
     * Принудительно сбрасывает кэшированный токен.
     * Полезно при получении 401 от API.
     */
    public function invalidateToken(): void
    {
        Yii::$app->cache->delete(self::TOKEN_CACHE_KEY);
    }

    // -----------------------------------------------------------------
    // Приватные методы
    // -----------------------------------------------------------------

    /**
     * Универсальный метод выполнения запроса.
     * При получении 401 — сбрасывает токен и повторяет запрос один раз.
     *
     * @throws MinimaxApiException
     */
    private function request(
        string $method,
        string $path,
        array  $queryParams = [],
        array  $body = [],
        bool   $isRetry = false,
    ): array {
        $token  = $this->getAccessToken();
        $baseUrl = $this->baseUrl . self::API_PREFIX;
        $client = new Client(['baseUrl' => $baseUrl]);

        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($path)
            ->addHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ]);

        if (!empty($queryParams)) {
            $request->setData($queryParams);
        }

        if (!empty($body)) {
            $request->setContent(json_encode($body));
        }

        try {
            $response = $request->send();
        } catch (HttpClientException $e) {
            throw new MinimaxApiException(
                "Ошибка соединения с Minimax API: {$e->getMessage()}",
                previous: $e
            );
        }

        // Токен протух — сбросить, повторить один раз
        if ($response->statusCode === 401 && !$isRetry) {
            $this->invalidateToken();
            return $this->request($method, $path, $queryParams, $body, isRetry: true);
        }

        if (!$response->isOk) {
            throw new MinimaxApiException(
                "Minimax API вернул ошибку {$response->statusCode} " .
                "для {$method} {$path}: {$response->content}"
            );
        }

        $data = json_decode($response->content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new MinimaxApiException(
                "Minimax API вернул невалидный JSON: {$response->content}"
            );
        }

        return $data ?? [];
    }

    /**
     * Запрашивает новый токен у Minimax OAuth2 и сохраняет в кэш.
     *
     * @throws MinimaxAuthException
     */
    private function fetchAndCacheToken(): string
    {
        $client = new Client(['baseUrl' => $this->baseUrl]);

        try {
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl(self::TOKEN_ENDPOINT)
                ->addHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
                ->setData([
                    'grant_type'    => 'password',
                    'username'      => $this->username,
                    'password'      => $this->password,
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope'         => 'minimax.rs',
                ])
                ->send();
        } catch (HttpClientException $e) {
            throw new MinimaxAuthException(
                "Не удалось подключиться к Minimax OAuth2: {$e->getMessage()}",
                previous: $e
            );
        }

        if (!$response->isOk) {
            throw new MinimaxAuthException(
                "Minimax OAuth2 вернул ошибку {$response->statusCode}: {$response->content}"
            );
        }

        $data = json_decode($response->content, true);

        if (empty($data['access_token']) || empty($data['expires_in'])) {
            throw new MinimaxAuthException(
                "Minimax OAuth2: неожиданный формат ответа: {$response->content}"
            );
        }

        // Кэшируем на время жизни токена минус буфер
        $ttl = (int)$data['expires_in'] - $this->tokenRefreshBuffer;

        Yii::$app->cache->set(self::TOKEN_CACHE_KEY, $data['access_token'], $ttl);

        Yii::info(
            "Minimax: получен новый токен, TTL={$ttl}s",
            'minimax'
        );

        return $data['access_token'];
    }
}