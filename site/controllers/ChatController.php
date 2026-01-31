<?php

namespace app\controllers;

use app\models\Customer;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;
use app\models\TelegramMessage;
use yii\db\Query;

class ChatController extends Controller
{
    /**
     * Отправка сообщения из интерфейса сайта в Telegram
     */
    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Json::decode(Yii::$app->request->getRawBody());
        $text = $data['message'] ?? '';
        $chat_id = $data['chat_id'] ?? '';

        if (empty($text)) {
            return ['success' => false];
        }

        // 1. Отправляем в Telegram (используйте ваш Bot Token и Chat ID)
        // $botToken = getenv('WORKERMAN_HOST');
        // $chatId = $data['chat_id'] ?? '';
        // $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        // $telegramResponse = file_get_contents($url . "?" . http_build_query([
        //     'chat_id' => $chatId,
        //     'text' => $text,
        //     'parse_mode' => 'HTML'
        // ]));
        $telegramResponse = true; // Заглушка для успешной отправки

        // 2. Если в Telegram ушло успешно, транслируем это сообщение всем в WebSocket
        if ($telegramResponse) {
            $this->broadcastToWebsockets([
                'username' => 'Менеджер сайта', // Или имя залогиненного пользователя
                'text' => $text,
                'message_type' => 'outgoing',
                'chat_id' => $chat_id,
                'date' => date('H:i')
            ]);
            return ['success' => true];
        }

        return ['success' => false];
    }

    public function actionBroadcast()
    {
        $this->layout = false;
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $data = Json::decode(Yii::$app->request->getRawBody());
        $message = $data['response'] ?? '';
        $userId = $data['user_id'] ?? 0;
        $chatId = $data['chat_id'] ?? 0;
        $messageType = 'outgoing';
        $messageId = $data['message_id'] ?? null;

        if (empty($message)) {
            $response->data = ['success' => false];
            return;
        }

        // Транслируем сообщение всем подключенным клиентам WebSocket
        $this->broadcastToWebsockets([
            'user_id' => $userId,
            'chat_id' => $chatId,
            'text' => $message,
            'date' => date('H:i'),
            'message_type' => $messageType,
            'message_id' => $messageId
        ]);

        $response->data = ['success' => true];
        return;
    }

    /**
     * Вспомогательный метод для связи с Workerman
     */
    private function broadcastToWebsockets($data)
    {
        // $fp = stream_socket_client("tcp://workerman:1234", $errno, $errstr, 3);
        $fp = stream_socket_client("tcp://" . getenv('WORKERMAN_HOST') . ":" . getenv('WORKERMAN_PORT'), $errno, $errstr, 3);
        if ($fp) {
            fwrite($fp, Json::encode($data) . "\n");
            fclose($fp);
        }
    }

    /**
     * Получение истории сообщений
     */
    public function actionHistory($id = null)
    {
        $this->layout = false;
        $limit = 30;
        Yii::$app->response->format = Response::FORMAT_JSON;

        // $messagesQuery = TelegramMessage::find()
        //     ->orderBy(['sent_at' => SORT_DESC]) // Берем последние
        //     ->limit($limit);

        $messagesQuery = (new Query())
            ->select(['tm.response', 'tm.message_type', 'tm.sent_at', 'c.username'])
            ->from(['tm' => TelegramMessage::tableName()])
            ->leftJoin(['c' => Customer::tableName()], 'c.tg_id = tm.chat_id')
            ->orderBy(['tm.sent_at' => SORT_DESC])
            ->limit($limit);

        if ($id !== null) {
            $messagesQuery->andWhere(['chat_id' => $id]);
        }

        $messages = $messagesQuery->all();

        // Переворачиваем массив, чтобы вверху были старые, а внизу — новые
        $messages = array_reverse($messages);

        return array_map(function ($model) {
            return [
                'text' => $model['response'],
                'username' => $model['username'] ?? 'Unknown',
                'date' => date('H:i', strtotime($model['sent_at'])),
                'is_my' => ($model['message_type'] === 'outgoing'),
            ];
        }, $messages);
    }
}
