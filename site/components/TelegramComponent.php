<?php

namespace app\components;

use Yii;
use yii\base\Component;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;
use app\models\TelegramMessage;
use app\models\TelegramChat;
use app\models\TelegramTopic;

class TelegramComponent extends Component
{
    public $botToken;
    private $bot;

    public function init()
    {
        parent::init();
        $this->bot = new BotApi($this->botToken);
        $hostName = Yii::$app->request->getHostName();
        $isSecure = Yii::$app->request->isSecureConnection;
        $protocol = $isSecure ? 'https' : 'http';
        $url = "{$protocol}://{$hostName}/telegram/webhook";
        $this->setWebHook($url);
    }

    public function setWebHook($url)
    {
        $webHookInfo = $this->getWebhookInfo();
        if (isset($webHookInfo['result']['url']) && $webHookInfo['result']['url'] === $url) {
            return;
        }
        $botToken = $this->botToken;
        $webhookUrl = "https://api.telegram.org/bot{$botToken}/setWebhook?url={$url}";
        try {
            $this->bot->setWebhook($url);
        } catch (\Exception $e) {
            // Логируем ошибку
            Yii::error("Failed to set webhook: " . $e->getMessage());
            try {
                // Пытаемся установить webhook через прямой запрос
                file_get_contents($webhookUrl);
            } catch (\Exception $ex) {
                Yii::error("Failed to set webhook via direct request: " . $ex->getMessage());
            }
        }
    }

    public function getWebhookInfo()
    {
        $botToken = $this->botToken;
        $webhookInfoUrl = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
        $response = file_get_contents($webhookInfoUrl);
        return json_decode($response, true);
    }

    public function processWebhook($data)
    {
        $update = Update::fromResponse($data);
        $message = $update->getMessage();

        if (!$message) {
            return;
        }

        $chatId = $message->getChat()->getId();
        $topicId = $message->getMessageThreadId(); // ID темы
        $userId = $message->getFrom()->getId();
        $userName = $message->getFrom()->getUsername() ?: $message->getFrom()->getFirstName();

        // Сохраняем чат
        $chat = TelegramChat::findOne(['chat_id' => $chatId]);
        if (!$chat) {
            $chat = new TelegramChat();
            $chat->chat_id = $chatId;
            $chat->title = $message->getChat()->getTitle() ?? '@' . $userName;
            $chat->type = $message->getChat()->getType();
            $chat->save();
        }

        // Сохраняем тему (если есть)
        if ($topicId) {
            $replyToMessage = $message->getReplyToMessage() ? $message->getReplyToMessage() : null;
            if ($replyToMessage) {
                $forumTopicCreated = $replyToMessage->getForumTopicCreated() ? $replyToMessage->getForumTopicCreated() : null;
                if ($forumTopicCreated) {
                    $topicName = $forumTopicCreated->getName();
                } else {
                    $topicName = 'Тема #' . $topicId;
                }
            } else {
                $topicName = 'Тема #' . $topicId;
            }
            $topic = TelegramTopic::findOne(['chat_id' => $chatId, 'topic_id' => $topicId]);
            if (!$topic) {
                $topic = new TelegramTopic();
                $topic->chat_id = $chatId;
                $topic->topic_id = $topicId;
                $topic->name = $topicName;
                $topic->save();
            }
        }

        // Сохраняем сообщение
        $msg = new TelegramMessage();
        $msg->message_id = $message->getMessageId();
        $msg->chat_id = $chatId;
        $msg->topic_id = $topicId;
        $msg->user_id = $userId;
        $msg->username = $userName;
        $msg->text = $message->getText();
        $msg->created_at = date('Y-m-d H:i:s');//, $message->getDate());
        $msg->is_outgoing = 0;
        $msg->save();
    }

    public function sendMessage($chatId, $topicId = null, $text = '')
    {
        $params = ['chat_id' => $chatId, 'text' => $text];

        if ($topicId) {
            $params['message_thread_id'] = $topicId;
        }

        $result = $this->bot->sendMessage(
            $params['chat_id'],
            $params['text'],
            null,
            false,
            null,
            null,
            false,
            $topicId
        );

        // Сохраняем отправленное сообщение
        $msg = new TelegramMessage();
        $msg->message_id = $result->getMessageId();
        $msg->chat_id = $chatId;
        $msg->topic_id = $topicId;
        $msg->user_id = $result->getFrom()->getId();
        $msg->username = 'Bot';
        $msg->text = $text;
        $msg->created_at = date('Y-m-d H:i:s');
        $msg->is_outgoing = 1;
        $msg->save();

        return [
            'send' => $result,
            'message' => $msg
        ];
    }
}
