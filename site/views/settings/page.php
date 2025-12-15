<?php

use app\components\SettingsTopWidget;
use app\components\SettingsDedlinesWidget;
use app\components\SettingsCalendarWidget;
use app\components\SettingsTemplatesWidget;

?>
<div class="p-6">
    <div class="space-y-6">
        <?= SettingsTopWidget::widget([
            'user' => $user,
        ]) ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= SettingsDedlinesWidget::widget([
                'user' => $user,
            ]) ?>
            <?= SettingsTemplatesWidget::widget([
                'user' => $user,
            ]) ?>


        </div>
        <?= SettingsCalendarWidget::widget([
            'user' => $user,
        ]) ?>
    </div>
</div>