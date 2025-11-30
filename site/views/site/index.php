<?php

/** @var yii\web\View $this */
use yii\helpers\Html;

$this->title = Yii::t('app', 'Главная');

// Пример данных (в реальном приложении будут получены из модели)
$dashboardData = [
    'totalClients' => 6,
    'activeTasks' => 7,
    'overdueTasks' => 2,
    'documentsForCheck' => 1,
];

$accountantLoad = [
    ['name' => 'Марина Иванова', 'tasks' => 4, 'total' => 9, 'color' => 'danger'],
    ['name' => 'Ольга Сидорова', 'tasks' => 5, 'total' => 8, 'color' => 'warning'],
    ['name' => 'Елена Козлова', 'tasks' => 2, 'total' => 6, 'color' => 'success'],
];

$upcomingDeadlines = [
    ['id' => 'Т-007', 'priority' => 'Высокий', 'company' => 'Restoran Balkan', 'description' => 'Просроченная декларация - требуется срочное внимание', 'status' => 'Просрочена', 'daysOverdue' => 377],
    // ... другие сроки
];

?>

<!-- Подключение Font Awesome для иконок (предполагается, что оно есть в AppAsset) -->

<div class="site-index">
    <h1 class="display-4"><?= Yii::t('app', 'Добро пожаловать, {name}!', ['name' => 'Анна']) ?></h1>
    <p class="text-muted"><?= Yii::t('app', 'Обзор') ?></p>

    <!-- Карточки ключевых метрик -->
    <div class="row mb-4">
        <?php foreach ($dashboardData as $key => $value): ?>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm rounded">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-muted"><?= Yii::t('app', ucfirst($key)) ?></h5>
                    <i class="fa fa-chart-bar text-danger"></i>
                </div>
                <h2 class="display-5 text-dark"><?= $value ?></h2>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <!-- Ближайшие сроки -->
        <div class="col-md-7">
            <div class="card p-3 shadow-sm rounded h-100">
                <h4 class="card-header border-0 bg-white"><?= Yii::t('app', 'Ближайшие сроки') ?></h4>
                <div class="card-body">
                    <?php foreach ($upcomingDeadlines as $deadline): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <span class="badge bg-<?= $deadline['priority'] === 'Высокий' ? 'danger' : 'info' ?>"><?= Yii::t('app', $deadline['priority']) ?></span>
                                <div><strong><?= $deadline['company'] ?></strong></div>
                                <small class="text-muted"><?= $deadline['description'] ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger"><?= $deadline['daysOverdue'] ?> <?= Yii::t('app', 'дней просрочено') ?></span>
                                <div><span class="badge bg-warning"><?= Yii::t('app', $deadline['status']) ?></span></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Загрузка бухгалтеров -->
        <div class="col-md-5">
            <div class="card p-3 shadow-sm rounded h-100">
                <h4 class="card-header border-0 bg-white"><?= Yii::t('app', 'Загрузка бухгалтеров') ?></h4>
                <div class="card-body">
                    <?php foreach ($accountantLoad as $load): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong><?= $load['name'] ?></strong>
                                <small class="text-muted"><?= $load['tasks'] ?> / <?= $load['total'] ?></small>
                            </div>
                            <!-- Имитация прогресс-бара -->
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-<?= $load['color'] ?>" role="progressbar"
                                     style="width: <?= ($load['tasks'] / $load['total']) * 100 ?>%;"
                                     aria-valuenow="<?= $load['tasks'] ?>" aria-valuemin="0" aria-valuemax="<?= $load['total'] ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
