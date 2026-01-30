<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;

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

        if (empty($text)) {
            return ['success' => false];
        }

        // 1. Отправляем в Telegram (используйте ваш Bot Token и Chat ID)
        $botToken = "ВАШ_ТОКЕН";
        $chatId = "ID_ВАШЕГО_ЧАТА";
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $telegramResponse = file_get_contents($url . "?" . http_build_query([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]));

        // 2. Если в Telegram ушло успешно, транслируем это сообщение всем в WebSocket
        if ($telegramResponse) {
            $this->broadcastToWebsockets([
                'username' => 'Менеджер сайта', // Или имя залогиненного пользователя
                'text' => $text,
                'date' => date('H:i')
            ]);
            return ['success' => true];
        }

        return ['success' => false];
    }

    /**
     * Вспомогательный метод для связи с Workerman
     */
    private function broadcastToWebsockets($data)
    {
        $fp = stream_socket_client("tcp://workerman:1234", $errno, $errstr, 3);
        if ($fp) {
            fwrite($fp, Json::encode($data) . "\n");
            fclose($fp);
        }
    }
    public function actionHistory($limit = 30)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $messages = \app\models\TelegramMessage::find()
            ->orderBy(['sent_at' => SORT_DESC]) // Берем последние
            ->limit($limit)
            ->all();

        // Переворачиваем массив, чтобы вверху были старые, а внизу — новые
        $messages = array_reverse($messages);

        return array_map(function ($model) {
            return [
                'text' => $model->response,
                'username' => ($model->message_type === 'incoming') ? 'Клиент' : 'Менеджер',
                'date' => date('H:i', strtotime($model->sent_at)),
                'is_my' => ($model->message_type === 'outgoing'),
            ];
        }, $messages);
    }
}
