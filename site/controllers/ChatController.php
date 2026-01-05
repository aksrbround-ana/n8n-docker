<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\httpclient\Client;
use yii\web\Response;

class ChatController extends Controller
{

    public function actionReceiveFromN8n()
    {
        $request = Yii::$app->request->post();

        // 1. Логика сохранения в БД $model->save()...
        // 2. Отправка в Pusher
        $data = [
            'message' => $request['text'],
            'from'    => $request['sender_name'],
            'time'    => date('H:i'),
        ];

        // 'chat-channel' — имя канала, 'new-message' — название события
        Yii::$app->pusher->trigger('chat-channel', 'new-message', $data);

        return ['status' => 'ok'];
    }

    public function actionSendMessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        // 1. Сохраняем в локальную БД
        // $message = new ChatMessage();
        // $message->text = $data['text'];
        // $message->save();

        // 2. Отправляем в n8n
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://your-n8n-webhook-url.com/webhook/chat-reply')
            ->setData([
                'text' => $data['text'],
                'chat_id' => $data['chat_id'], // Телеграм ID получателя
                'sender' => 'operator',
            ])
            ->send();

        if ($response->isOk) {
            return ['status' => 'success'];
        } else {
            Yii::error("Ошибка n8n: " . $response->content);
            return ['status' => 'error', 'message' => 'n8n unreachable'];
        }
    }
}
