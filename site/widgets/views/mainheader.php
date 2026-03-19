<?php

use app\services\DictionaryService;
use app\services\ReminderService;

$initials = strtoupper($user->firstname[0] . $user->lastname[0]);
$doneCount = ReminderService::doneCount();
?>
<header class="fixed top-0 right-0 left-64 z-30 h-16 bg-card border-b border-border flex items-center justify-between px-6">
    <div id="reminder-container" class="flex-1">
        <div id="reminder-done" class="<?= $doneCount > 0 ? '' : 'hidden' ?>">
            <?= DictionaryService::getWord('doneReminders', $user->lang) ?>: <span class="count"><?= $doneCount ?></span>
        </div>
        <div id="reminder-list" class="hidden text-center py-2">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class=""><?= DictionaryService::getWord('company', $user->lang) ?></th>
                        <th class=""><?= DictionaryService::getWord('text', $user->lang) ?></th>
                        <th class=""><?= DictionaryService::getWord('reminderDate', $user->lang) ?></th>
                    </tr>
                </thead>
                <tbody id="reminder-tbody"></tbody>
            </table>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <button id="user-card-mini" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50    hover:bg-accent hover:text-accent-foreground gap-3 h-auto py-2 px-3" type="button" aria-haspopup="menu" aria-expanded="false" data-state="closed">
            <span class="relative flex shrink-0 overflow-hidden rounded-full h-8 w-8">
                <span class="flex h-full w-full items-center justify-center rounded-full bg-primary text-primary-foreground text-xs"><?= $initials ?></span>
            </span>
            <div class="flex flex-col items-start">
                <span class="ptm-name text-sm font-medium"><?= $user->firstname . ' ' . $user->lastname ?></span>
                <div class="ptm-position inline-flex items-center rounded-full border font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80 text-[10px] px-1.5 py-0 h-4"><?= DictionaryService::getWord($user->rule, $user->lang) ?></div>
            </div>
        </button>
    </div>
</header>