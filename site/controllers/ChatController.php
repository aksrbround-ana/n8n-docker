<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\Cors;

class ChatController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send-message' => ['POST'],
                    'get-messages' => ['GET'],
                ],
            ],
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400,
                ],
            ],
        ];
    }

    /**
     * Отправка сообщения оператором
     */
    public function actionSendMessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $chatId = Yii::$app->request->post('chat_id');
        $operatorId = Yii::$app->request->post('operator_id');
        $operatorName = Yii::$app->request->post('operator_name');
        $responseText = Yii::$app->request->post('response_text');

        if (!$chatId || !$responseText) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'error' => 'Missing required parameters: chat_id and response_text'
            ];
        }

        // URL вашего n8n webhook для исходящих сообщений
        $n8nWebhookUrl = $this->getN8nWebhookBaseUrl() . 'telegram-outgoing';
        
        $postData = [
            'chat_id' => (int)$chatId,
            'operator_id' => (int)$operatorId ?: null,
            'operator_name' => $operatorName ?: 'Оператор',
            'response_text' => $responseText,
        ];

        try {
            // Отправка в n8n
            $ch = curl_init($n8nWebhookUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode === 200) {
                $result = json_decode($response, true);
                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Сообщение отправлено в Telegram'
                ];
            } else {
                Yii::error("n8n webhook error: HTTP $httpCode - $response", __METHOD__);
                Yii::$app->response->statusCode = 500;
                return [
                    'success' => false,
                    'error' => 'Failed to send message to n8n',
                    'details' => $response
                ];
            }
        } catch (\Exception $e) {
            Yii::error("Exception in send-message: " . $e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение истории сообщений для чата
     */
    public function actionGetMessages($chatId, $limit = 50)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$chatId) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'error' => 'Missing chat_id parameter'
            ];
        }

        try {
            $db = Yii::$app->db;
            
            // Получаем входящие сообщения
            $incomingMessages = $db->createCommand('
                SELECT 
                    id,
                    chat_id,
                    user_id,
                    username,
                    message_text as text,
                    \'incoming\' as type,
                    created_at as timestamp
                FROM telegram_messages
                WHERE chat_id = :chat_id
                ORDER BY created_at DESC
                LIMIT :limit
            ', [
                ':chat_id' => (int)$chatId,
                ':limit' => (int)$limit
            ])->queryAll();

            // Получаем исходящие сообщения
            $outgoingMessages = $db->createCommand('
                SELECT 
                    id,
                    chat_id,
                    operator_id,
                    operator_name,
                    response_text as text,
                    \'outgoing\' as type,
                    created_at as timestamp
                FROM operator_responses
                WHERE chat_id = :chat_id
                ORDER BY created_at DESC
                LIMIT :limit
            ', [
                ':chat_id' => (int)$chatId,
                ':limit' => (int)$limit
            ])->queryAll();

            // Объединяем и сортируем по времени
            $allMessages = array_merge($incomingMessages, $outgoingMessages);
            usort($allMessages, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // Ограничиваем до limit
            $allMessages = array_slice($allMessages, 0, (int)$limit);

            return [
                'success' => true,
                'chat_id' => (int)$chatId,
                'messages' => $allMessages,
                'total' => count($allMessages)
            ];
        } catch (\Exception $e) {
            Yii::error("Exception in get-messages: " . $e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение списка активных чатов
     */
    public function actionGetActiveChats()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $db = Yii::$app->db;
            
            $chats = $db->createCommand('
                SELECT DISTINCT
                    chat_id,
                    MAX(username) as username,
                    MAX(created_at) as last_message_at,
                    COUNT(*) as message_count
                FROM telegram_messages
                GROUP BY chat_id
                ORDER BY MAX(created_at) DESC
                LIMIT 100
            ')->queryAll();

            return [
                'success' => true,
                'chats' => $chats,
                'total' => count($chats)
            ];
        } catch (\Exception $e) {
            Yii::error("Exception in get-active-chats: " . $e->getMessage(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}