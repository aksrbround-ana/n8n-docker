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

        // Хранилище активных соединений
        $wsWorker->connections = [];

        $wsWorker->onConnect = function ($connection) {
            echo "New connection established\n";
        };

        // 2. Создаем внутренний TCP-сервер для приема данных от Yii2 (внутри Docker сети)
        // Слушаем 0.0.0.0, чтобы принимать запросы из контейнера php-site
        $innerWorker = new Worker("text://0.0.0.0:1234");

        $innerWorker->onMessage = function ($connection, $buffer) use ($wsWorker) {
            // Ожидаем, что Yii2 пришлет JSON строку
            echo "Received data from backend: $buffer\n";

            // Рассылаем сообщение всем подключенным браузерам
            foreach ($wsWorker->connections as $clientConnection) {
                $clientConnection->send($buffer);
            }
        };

        // Запускаем оба воркера
        Worker::runAll();
    }
}
