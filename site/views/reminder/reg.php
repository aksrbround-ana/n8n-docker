<?php

use app\components\RegRemindersTableWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="grid grid-cols-1 md:grid-cols-1 gap-4">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow cursor-pointer">
        <div class="flex flex-col space-y-1.5 p-6 pb-3">
            <div class="flex items-center gap-3 grid grid-cols-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <?= SvgService::svg('bell') ?>
                </div>
                <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('reminders', $user->lang) ?></h3>
            </div>
        </div>
        <?= RegRemindersTableWidget::widget([
            'user' => $user,
            'reminders' => $reminders
        ]) ?>
    </div>
</div>