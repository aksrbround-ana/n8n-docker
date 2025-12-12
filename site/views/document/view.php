<?php

use app\components\DocViewActionsWidget;
use app\components\DocViewTopWidget;
use app\components\DocViewInfoWidget;
use app\components\DocViewCompanyWidget;

?>
<div class="p-6">
    <div class="space-y-6">
        <?= DocViewTopWidget::widget([
            'user' => $user,
            'document' => $document,
        ]) ?>
        <div class="grid gap-6 lg:grid-cols-3">
            <?= DocViewInfoWidget::widget([
                'user' => $user,
                'document' => $document,
            ]) ?>
            <div class="space-y-6">
                <?= DocViewCompanyWidget::widget([
                    'user' => $user,
                    'document' => $document,
                ])
                ?>
                <?= DocViewActionsWidget::widget([
                    'user' => $user,
                    'document' => $document,
                ])
                ?>
            </div>
        </div>
    </div>
</div>