<?php

/** @var yii\web\View $this */
use yii\helpers\Html;

$this->title = Yii::t('app', 'Компании');

// Пример данных (в реальном приложении будут получены из модели)
$companies = [
    [
        'name' => 'BuildPro Construcije',
        'pib' => '556789012',
        'status' => 'Активная',
        'city' => 'Белград',
        'activity' => 'Строительство',
        'manager' => 'Ольга Сидорова',
        'openTasks' => 6,
        'overdueTasks' => 1,
    ],
    [
        'name' => 'EcoFarm Srbija',
        'pib' => '607890123',
        'status' => 'Приостановлена',
        'city' => 'Суботица',
        'activity' => 'Сельское хозяйство',
        'manager' => 'Елена Козлова',
        'openTasks' => 0,
        'overdueTasks' => 0,
    ],
    // ... другие компании
];

?>

<div class="company-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Yii::t('app', 'Компании') ?> <small class="text-muted">(<?= count($companies) ?>)</small></h1>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Добавить компанию'), ['create'], ['class' => 'btn btn-danger']) ?>
    </div>

    <!-- Панель фильтров и поиска -->
    <div class="d-flex mb-4">
        <input type="text" class="form-control me-2" placeholder="<?= Yii::t('app', 'Поиск компаний...') ?>">
        <button class="btn btn-outline-secondary me-2"><i class="fa fa-filter"></i> <?= Yii::t('app', 'Отфильтровать') ?></button>
    </div>

    <!-- Карточное представление -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($companies as $company): ?>
        <div class="col">
            <div class="card shadow-sm rounded h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title"><?= Html::encode($company['name']) ?></h5>
                        <?php
                            $badgeClass = '';
                            if ($company['status'] === 'Активная') {
                                $badgeClass = 'bg-success';
                            } elseif ($company['status'] === 'Приостановлена') {
                                $badgeClass = 'bg-warning';
                            }
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= Yii::t('app', $company['status']) ?></span>
                    </div>
                    <small class="text-muted"><?= Yii::t('app', 'ИНН') ?>: <?= Html::encode($company['pib']) ?></small>
                    <hr>
                    <p class="card-text">
                        <i class="fa fa-map-marker-alt text-muted me-2"></i> <?= Html::encode($company['city']) ?><br>
                        <i class="fa fa-briefcase text-muted me-2"></i> <?= Yii::t('app', Html::encode($company['activity'])) ?><br>
                        <i class="fa fa-user-tie text-muted me-2"></i> <?= Html::encode($company['manager']) ?>
                    </p>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small>
                            <i class="fa fa-tasks text-muted me-1"></i>
                            <?= $company['openTasks'] ?> <?= Yii::t('app', 'Открытые задачи') ?>
                        </small>
                        <?php if ($company['overdueTasks'] > 0): ?>
                        <small class="text-danger">
                            <i class="fa fa-exclamation-triangle me-1"></i>
                            <?= $company['overdueTasks'] ?>
                        </small>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between">
                        <?= Html::a(Yii::t('app', 'Открыть профиль'), ['view', 'id' => $company['pib']], ['class' => 'btn btn-danger flex-grow-1 me-2']) ?>
                        <button class="btn btn-outline-secondary"><i class="fa fa-file-alt"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
