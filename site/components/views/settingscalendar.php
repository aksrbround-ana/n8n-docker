<?php

use app\components\SettingsCalendarBodyWidget;
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
                <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('taxCalendar', $user->lang) ?></h3>
                <select id="tax-calendar-month" class="ml-auto">
                    <?php
                    foreach ($monthList as $monthRaw) {
                        list($year, $monthNum) = explode('-', $monthRaw);

                    ?>
                        <option value="<?= $monthRaw ?>" <?= $monthNum == $month ? 'selected' : '' ?>><?= ucfirst(DictionaryService::getWord(DictionaryService::$months[$monthNum - 1], $user->lang)) . ' ' . $year ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="bg-card">
                <input type="text" id="tax-calendar-page-url" class="input input-sm input-bordered border rounded-md" placeholder="Url" style="width: 100%;" />
                <p id="tax-calendar-page-url-message" class="border rounded-md" style="text-align: center;display:none;"><?= DictionaryService::getWord('yourRequestHasBeenSent', $user->lang) ?></p>
                <button id="tax-calendar-page-parse" class="btn btn-sm btn-primary" style="width: 100%;"><?= DictionaryService::getWord('loadTaxCalendarPage', $user->lang) ?></button>
            </div>
        </div>
        <?=
        SettingsCalendarBodyWidget::widget([
            'taxCalendar' => $taxCalendar,
            'user' => $user,
        ]);
        ?>
    </div>
</div>