<?php

use app\components\RegReminderTableRowWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<table id="reg-reminder-table" class="w-full table-auto">
    <thead>
        <tr class="border-t bg-muted/50">
            <th class="p-6 text-left text-sm font-semibold tracking-tight">ID</th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('deadline_day', $user->lang)  ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('type_ru', $user->lang)  ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('type_rs', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('text_ru', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('text_rs', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"></th>
        </tr>
    </thead>
    <tbody id="reg-reminder-table-body">
        <?php
        foreach ($reminders as $item) {
            $class = ['reg-reminder-btn'];
            echo RegReminderTableRowWidget::widget([
                'user' => $user,
                'reminder' => $item,
                'class' => $class,
            ]);
        ?>
        <?php
        }
        ?>
    </tbody>
</table>