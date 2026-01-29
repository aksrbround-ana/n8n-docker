<?php

namespace app\helpers;

/**
 * Helper для работы с Centrifugo
 * 
 * Требует установки: composer require firebase/php-jwt
 */
class CentrifugoHelper
{
    /**
     * Генерация Connection Token для пользователя
     * 
     * @param int|string $userId ID пользователя
     * @param int $ttl Время жизни токена в секундах (по умолчанию 3600 = 1 час)
     * @return string JWT токен
     */
    public static function generateConnectionToken($userId, $ttl = 3600)
    {
        $secret = getenv('CENTRIFUGO_TOKEN_SECRET');
        
        if (!$secret) {
            throw new \Exception('CENTRIFUGO_TOKEN_SECRET not set in environment');
        }
        
        $claims = [
            'sub' => (string)$userId,
            'exp' => time() + $ttl,
            'iat' => time(),
        ];
        
        return \Firebase\JWT\JWT::encode($claims, $secret, 'HS256');
    }

    /**
     * Генерация Subscription Token для подписки на конкретный канал
     * 
     * @param int|string $userId ID пользователя
     * @param string $channel Название канала
     * @param int $ttl Время жизни токена в секундах
     * @return string JWT токен
     */
    public static function generateSubscriptionToken($userId, $channel, $ttl = 3600)
    {
        $secret = getenv('CENTRIFUGO_TOKEN_SECRET');
        
        if (!$secret) {
            throw new \Exception('CENTRIFUGO_TOKEN_SECRET not set in environment');
        }
        
        $claims = [
            'sub' => (string)$userId,
            'channel' => $channel,
            'exp' => time() + $ttl,
            'iat' => time(),
        ];
        
        return \Firebase\JWT\JWT::encode($claims, $secret, 'HS256');
    }

    /**
     * Публикация сообщения в канал Centrifugo через HTTP API
     * 
     * @param string $channel Название канала
     * @param array $data Данные для отправки
     * @return array|false Ответ от Centrifugo или false при ошибке
     */
    public static function publish($channel, $data)
    {
        $apiKey = getenv('CENTRIFUGO_API_KEY');
        $centrifugoUrl = 'http://centrifugo:8000/api';
        
        if (!$apiKey) {
            throw new \Exception('CENTRIFUGO_API_KEY not set in environment');
        }
        
        $payload = [
            'method' => 'publish',
            'params' => [
                'channel' => $channel,
                'data' => $data,
            ],
        ];
        
        $ch = curl_init($centrifugoUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-API-Key: ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 5,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        \Yii::error("Centrifugo publish error: HTTP $httpCode - $response", __METHOD__);
        return false;
    }

    /**
     * Отправка события о новом сообщении в чат
     * 
     * @param int $chatId ID чата
     * @param array $messageData Данные сообщения
     * @return bool Успех операции
     */
    public static function notifyNewMessage($chatId, $messageData)
    {
        $channel = "chat:{$chatId}";
        $result = self::publish($channel, $messageData);
        
        return $result !== false;
    }

    /**
     * Получение информации о канале
     * 
     * @param string $channel Название канала
     * @return array|false Информация о канале или false при ошибке
     */
    public static function getChannelInfo($channel)
    {
        $apiKey = getenv('CENTRIFUGO_API_KEY');
        $centrifugoUrl = 'http://centrifugo:8000/api';
        
        if (!$apiKey) {
            throw new \Exception('CENTRIFUGO_API_KEY not set in environment');
        }
        
        $payload = [
            'method' => 'info',
            'params' => [
                'channel' => $channel,
            ],
        ];
        
        $ch = curl_init($centrifugoUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-API-Key: ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 5,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return false;
    }
}