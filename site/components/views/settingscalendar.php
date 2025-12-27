<?php

use app\components\SettingsCalendarBodyWidget;
use app\services\DictionaryService;
// print_r($taxCalendar);
?>
<div class="grid grid-cols-1 md:grid-cols-1 gap-4">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow cursor-pointer">
        <div class="flex flex-col space-y-1.5 p-6 pb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell h-5 w-5 text-primary">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                    </svg>
                </div>
                <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('taxCalendar', $user->lang) ?> <?php //= ucfirst(DictionaryService::getWord(DictionaryService::$months[$month - 1], $user->lang)) . ' ' . $year ?></h3>
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
                <div class="">
                    <input type="text" id="tax-calendar-page-url" class="input input-sm input-bordered border rounded-md" placeholder="Url" style="width: 100%;" />
                    <button id="tax-calendar-page-parse" class="btn btn-sm btn-primary" style="width: 100%;"><?= DictionaryService::getWord('loadTaxCalendarPage', $user->lang) ?></button>
                </div>
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