<?php

use app\components\SettingsCalendarBodyWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="grid grid-cols-1 md:grid-cols-1 gap-4">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow cursor-pointer">
        <div class="flex flex-col space-y-1.5 p-6 pb-3">
            <div class="flex items-center gap-3 grid grid-cols-3">
                <div class="ml-auto align-left">
                <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('taxCalendar', $user->lang) ?></h1>
                </div>
                <div class="align-left">
                <select id="tax-calendar-month" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background w-48">
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
                <div class="ml-auto">
                    <!-- <button id="tax-calendar-load-month" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background w-48" style="float: right;"><?= DictionaryService::getWord('loadMonth', $user->lang) ?></button> -->
                    <button id="tax-calendar-load-month" class="btn btn-sm ml-2 p-3 rounded-md bg-primary border border-input font-medium inline-flex items-center justify-center text-sm text-primary-foreground whitespace-nowrap m3" style="float: right;"><?= DictionaryService::getWord('loadMonth', $user->lang) ?></button>
                    <select id="tax-calendar-month_to_load" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background w-48" style="float: right;">
                        <?php
                        $monthLoad = $month;
                        $yearLoad = $year;
                        for ($i = 0; $i < 3; $i++) {
                            $monthValue = str_pad($monthLoad, 2, '0', STR_PAD_LEFT);
                            $monthLabel = ucfirst(DictionaryService::getWord(DictionaryService::$months[$monthLoad - 1], $user->lang)) . ' ' . $yearLoad;   
                            $disabled = in_array ($yearLoad . '-' . $monthValue, $monthList) ? 'disabled' : '';
                            ?>
                            <option value="<?= $yearLoad . '-' . $monthValue ?>" <?= $disabled ?>><?= $monthLabel ?></option>
                            <?php
                            $monthLoad++;
                            if ($monthLoad == 13) {
                                $monthLoad = 1;
                                $yearLoad++;
                            }
                        }
                        ?>
                    </select>
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