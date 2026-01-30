<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\components\ModalCreateRegReminderWidget;
use app\components\ModalEditCalendarWidget;
use app\components\ModalWindowWidget;

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$centrifugoUrl = getenv('CENTRIFUGO_URL');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUHGALTERIJA - Бэк-офис</title>
    <meta name="description" content="Внутренняя система управления бухгалтерской компанией BUHGALTERIJA">
    <meta name="author" content="BUHGALTERIJA">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,&lt;svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'&gt;&lt;text y='.9em' font-size='90'&gt;📊&lt;/text&gt;&lt;/svg&gt;">
    <script src="/js/jquery.js"></script>
    <script src="/js/index.js"></script>
    <script src="/js/dictionary.js"></script>
    <script src="/js/modal.js"></script>
    <script src="/js/tiff.min.js"></script>
    <script src="/js/select.js"></script>
    <!-- <script src="/js/centrifugo.js"></script> -->
    <script src="https://unpkg.com/centrifuge@3.1.0/dist/centrifuge.js"></script>
    <script src="/js/chat.js"></script>
    <script>
        const CONFIG = {
            centrifugoUrl: 'wss://<?= getenv('CENTRIFUGO_URL') ?>/connection/websocket',
            centrifugoToken: '<?= getenv('CENTRIFUGO_TOKEN_SECRET') ?>', // Генерируется на сервере
            yii2ApiUrl: 'https://<?= getenv('PORTAL_URL') ?>',
            operatorId: 1, // ID текущего оператора
            operatorName: 'Оператор'
        };
        // const centrifuge = new Centrifuge('ws://<?= $centrifugoUrl ?>:8000/connection/websocket');
        // const sub = centrifuge.newSubscription('public:messages');
        // sub.on('publication', function(ctx) {
        //     const msg = ctx.data.text;
        //     // Добавляем сообщение в верстку
        //     document.getElementById('chat-box').innerHTML += `<p>${msg}</p>`;
        // });
        // sub.subscribe();
        // centrifuge.connect();
    </script>
    <link rel="stylesheet" href="/css/site.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/modal.css">
    <link rel="stylesheet" href="/css/select.css">
    <link rel="stylesheet" href="/css/chat.css">
</head>

<body>
    <div id="root"></div>
    <div id="error-tab" role="region" aria-label="Notifications (F8)" tabindex="-1" style="pointer-events: none;">
        <ol tabindex="-1" class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]"></ol>
    </div>
    <?= ModalWindowWidget::widget([
        'user' => Yii::$app->view->params['accountant'],
    ]) ?>
    <?= ModalEditCalendarWidget::widget([
        'user' => Yii::$app->view->params['accountant'],
        'token' => Yii::$app->view->params['token'],
    ]) ?>
    <?= ModalCreateRegReminderWidget::widget([
        'user' => Yii::$app->view->params['accountant'],
    ]) ?>
</body>

</html>
<?php $this->endPage() ?>