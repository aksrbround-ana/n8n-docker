<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Задачи');

?>

<div class="task-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Yii::t('app', 'Задачи') ?> <small class="text-muted">(8 <?= Yii::t('app', 'задач') ?>)</small></h1>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Создать задачу'), ['create'], ['class' => 'btn btn-danger']) ?>
    </div>

    <!-- Панель фильтров и поиска -->
    <div class="d-flex mb-4 align-items-center">
        <input type="text" class="form-control me-2" placeholder="<?= Yii::t('app', 'Поиск задач...') ?>">
        <button class="btn btn-outline-secondary me-2"><i class="fa fa-filter"></i> <?= Yii::t('app', 'Фильтры') ?></button>
        <button class="btn btn-outline-secondary me-2"><i class="fa fa-sync"></i> <?= Yii::t('app', 'Сбросить') ?></button>
    </div>

    <?php
    // Предполагается, что у вас есть модель Task и ActiveDataProvider
    // Ниже приведена простая имитация GridView, чтобы показать структуру
    ?>
    <table class="table table-hover bg-white rounded shadow-sm">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th><?= Yii::t('app', 'Название компании') ?></th>
                <th><?= Yii::t('app', 'Тип задачи') ?></th>
                <th><?= Yii::t('app', 'Период') ?></th>
                <th><?= Yii::t('app', 'Статус') ?></th>
                <th><?= Yii::t('app', 'Приоритет') ?></th>
                <th><?= Yii::t('app', 'Срок') ?></th>
                <th><?= Yii::t('app', 'Исполнитель') ?></th>
                <th><?= Yii::t('app', 'Обновлено') ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Т-007</td>
                <td>Restoran Balkan</td>
                <td>НДС декларация</td>
                <td>Октябрь 2024</td>
                <td><span class="badge bg-danger">Просрочена</span></td>
                <td><span class="text-danger">Высокий</span></td>
                <td>15.11.2024</td>
                <td>Марина Иванова</td>
                <td>20.11, 16:30</td>
                <td><i class="fa fa-ellipsis-v"></i></td>
            </tr>
            <tr>
                <td>Т-003</td>
                <td>Global Trade SRB</td>
                <td>Сверка</td>
                <td>Ноябрь 2024</td>
                <td><span class="badge bg-success">Выполнена</span></td>
                <td><span class="text-info">Обычный</span></td>
                <td>25.11.2024</td>
                <td>Ольга Сидорова</td>
                <td>24.11, 17:00</td>
                <td><i class="fa fa-ellipsis-v"></i></td>
            </tr>
            <!-- ... другие строки задач, имитирующие GridView ... -->
        </tbody>
    </table>

    <!-- В реальном приложении это будет GridView -->
    <?php /*
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'tableOptions' => ['class' => 'table table-hover bg-white rounded shadow-sm'],
        'columns' => [
            // ... настройки колонок
        ],
    ]);
    */ ?>
</div>
