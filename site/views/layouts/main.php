<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

// Текущий язык
$currentLanguage = Yii::$app->language;

// Определяем роль пользователя для отображения
$userRole = 'Гость';
if (!Yii::$app->user->isGuest) {
    // В реальном приложении нужно получить роль из authManager
    // Для примера:
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
    $userRole = Yii::$app->authManager->getRole(key($roles))->description ?? 'Пользователь';
}
// Получаем имя пользователя (пример)
$userName = Yii::$app->user->isGuest ? 'Гость' : 'Анна Петрова';

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- В реальном проекте здесь будет подключение Tailwind/Bootstrap или вашего CSS -->
    <style>
        /* Базовые стили для имитации дизайна со скриншотов */
        body { font-family: 'Inter', sans-serif; background-color: #f7f7f7; }
        .wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 200px; background-color: #212529; color: #fff; padding-top: 20px; flex-shrink: 0; }
        .logo { color: #f8f9fa; font-size: 1.5rem; padding: 0 15px 20px; font-weight: bold; }
        .menu-item { padding: 10px 15px; margin-bottom: 5px; cursor: pointer; display: flex; align-items: center; }
        .menu-item.active, .menu-item:hover { background-color: #e84c3d; color: #fff; border-radius: 4px; }
        .menu-item a { color: inherit; text-decoration: none; display: block; width: 100%; }
        .menu-item i { margin-right: 10px; }
        .content-area { flex-grow: 1; display: flex; flex-direction: column; }
        .header { background-color: #fff; padding: 10px 20px; box-shadow: 0 2px 4px rgba(0,0,0,.05); display: flex; justify-content: space-between; align-items: center; }
        .search-box { width: 400px; padding: 8px 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f8f9fa; }
        .user-panel { display: flex; align-items: center; }
        .lang-switch { margin-right: 15px; font-weight: bold; }
        .notifications { margin-right: 15px; }
        .user-info { margin-left: 10px; text-align: right; }
        .user-info .role { font-size: 0.8rem; color: #6c757d; }
        .avatar { width: 40px; height: 40px; background-color: #e84c3d; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; margin-left: 10px; }
        .main-content { padding: 20px; flex-grow: 1; }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<div class="wrapper">
    <!-- Боковое меню (Sidebar) -->
    <div class="sidebar">
        <div class="logo">BUHGALTERIJA</div>
        <nav>
            <?php
            $menuItems = [
                ['label' => Yii::t('app', 'Главная'), 'url' => ['/site/index'], 'icon' => 'fa fa-home'],
                ['label' => Yii::t('app', 'Компании'), 'url' => ['/company/index'], 'icon' => 'fa fa-building'],
                ['label' => Yii::t('app', 'Задачи'), 'url' => ['/task/index'], 'icon' => 'fa fa-tasks'],
                ['label' => Yii::t('app', 'Документы'), 'url' => ['/document/index'], 'icon' => 'fa fa-file-alt'],
                ['label' => Yii::t('app', 'Отчеты'), 'url' => ['/report/index'], 'icon' => 'fa fa-chart-line'],
                ['label' => Yii::t('app', 'Настройки'), 'url' => ['/settings/index'], 'icon' => 'fa fa-cog', 'active' => Yii::$app->controller->id === 'settings'],
            ];

            foreach ($menuItems as $item) {
                // Активный класс для имитации дизайна
                $activeClass = Yii::$app->request->url === Url::to($item['url']) ? 'active' : '';
                // Проверка прав доступа (реальная реализация требует проверки через Yii::$app->user->can())
                $isVisible = true;
                if ($item['label'] === Yii::t('app', 'Настройки') && !Yii::$app->user->can('systemSettings')) {
                    // $isVisible = false;
                }

                if ($isVisible) {
                    echo Html::tag('div',
                        Html::a('<i class="' . $item['icon'] . '"></i>' . $item['label'], $item['url']),
                        ['class' => 'menu-item ' . $activeClass]
                    );
                }
            }
            ?>
        </nav>
    </div>

    <div class="content-area">
        <!-- Шапка (Header) -->
        <div class="header">
            <input type="text" class="search-box" placeholder="<?= Yii::t('app', 'Поиск по компаниям, ПИБ, задаче...') ?>">
            <div class="user-panel">
                <!-- Переключение языка -->
                <div class="lang-switch">
                    <?= Html::a('RU', Url::current(['language' => 'ru-RU']), ['class' => $currentLanguage === 'ru-RU' ? 'text-danger' : '']) ?>
                    |
                    <?= Html::a('SR', Url::current(['language' => 'sr-SP']), ['class' => $currentLanguage === 'sr-SP' ? 'text-danger' : '']) ?>
                </div>
                <!-- Уведомления (колокольчик) -->
                <div class="notifications">
                    <i class="fa fa-bell"></i>
                    <span class="badge bg-danger">3</span>
                </div>
                <!-- Панель пользователя -->
                <div class="user-info">
                    <div class="name"><?= Html::encode($userName) ?></div>
                    <div class="role"><?= Yii::t('app', $userRole) ?></div>
                </div>
                <div class="avatar"><?= substr($userName, 0, 1) ?></div>
            </div>
        </div>

        <!-- Основное содержимое -->
        <div class="main-content">
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
