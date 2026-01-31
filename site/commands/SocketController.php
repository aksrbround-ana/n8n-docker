<?php

namespace app\commands;

use yii\console\Controller;
use Workerman\Worker;
use yii\helpers\Json;

/**
 * Контроллер для запуска WebSocket сервера через Workerman.
 * Запуск: php yii socket/start
 */
class SocketController extends Controller
{
    // 1. Создаем статическое хранилище
    public static $connections = [];

    public function actionStart()
    {
        // 1. Запускаем Channel-сервер (внутренняя шина)
        $channelServer = new \Channel\Server('0.0.0.0', 2206);

        // 2. WebSocket сервер
        $wsWorker = new Worker("websocket://0.0.0.0:2346");
        $wsWorker->count = 1;
        $wsWorker->name = 'ChatWS';

        $wsWorker->onWorkerStart = function () {
            // Подключаемся к локальной шине Channel
            \Channel\Client::connect('127.0.0.1', 2206);

            // Подписываемся на событие "broadcast"
            \Channel\Client::on('send_to_all', function ($data) {
                global $wsWorker;
                foreach ($wsWorker->connections as $connection) {
                    $connection->send($data);
                }
            });
        };

        // 3. Внутренний API сервер (принимает данные от n8n)
        $innerWorker = new Worker("text://0.0.0.0:1234");
        $innerWorker->count = 1;
        $innerWorker->name = 'InternalAPI';

        $innerWorker->onWorkerStart = function () {
            \Channel\Client::connect('127.0.0.1', 2206);
        };

        $innerWorker->onMessage = function ($connection, $buffer) {
            echo "Received from n8n: $buffer\n";
            // Просто пушим данные в шину Channel
            \Channel\Client::publish('send_to_all', $buffer);
            $connection->send("ok");
        };

        Worker::runAll();
    }
}
