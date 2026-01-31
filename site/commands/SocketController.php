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
    public function actionStart()
    {
        // 1. Создаем WebSocket-сервер для браузеров (вход через Traefik)
        // Порт должен совпадать с тем, что указан в labels traefik (2346)
        $wsWorker = new Worker("websocket://0.0.0.0:2346");
        $wsWorker->count = 1;
        $wsWorker->connections = [];

        $wsWorker->onConnect = function ($connection) use (&$wsWorker) {
            echo "New connection established. ID: {$connection->id}\n";
            // ОБЯЗАТЕЛЬНО добавляем соединение в список, иначе foreach будет пустым
            $wsWorker->connections[$connection->id] = $connection;
        };

        // Также стоит удалять их при отключении, чтобы не копить мусор
        $wsWorker->onClose = function ($connection) use (&$wsWorker) {
            echo "Connection closed. ID: {$connection->id}\n";
            unset($wsWorker->connections[$connection->id]);
        };

        // 2. Создаем внутренний TCP-сервер для приема данных от Yii2 (внутри Docker сети)
        // Слушаем 0.0.0.0, чтобы принимать запросы из контейнера php-site
        $innerWorker = new Worker("text://0.0.0.0:1234");
        $innerWorker->count = 1;

        $innerWorker->onMessage = function ($connection, $buffer) use (&$wsWorker) {
            echo "Received data from backend: $buffer\n";

            // Workerman сам наполняет $wsWorker->connections
            $allConnections = $wsWorker->connections;
            echo "Total connections: " . count($allConnections) . "\n";

            foreach ($allConnections as $clientConnection) {
                echo "Sending to connection {$clientConnection->id}\n";
                $clientConnection->send($buffer);
            }
            $connection->send("ok");
        };

        // Запускаем оба воркера
        Worker::runAll();
    }
}
