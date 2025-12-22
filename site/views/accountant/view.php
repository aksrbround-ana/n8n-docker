<?php

use app\components\TaskListWidget;
use app\services\DictionaryService;

?>
<div class="p-6">
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <?php
            // if ($back) {
            ?>
            <button class="back inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-5 w-5">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
            </button>
            <?php
            // }
            ?>
            <div>
                <h1 class="text-2xl font-heading font-bold"><?= $accountant->firstname . ' ' . $accountant->lastname ?></h1>
                <p class="text-muted-foreground mt-1"><?= count($tasks) ?> <?= DictionaryService::getWord('tasks', $user->lang) ?></p>
            </div>
        </div>
        <div class="border rounded-lg overflow-hidden">
            <div class="relative w-full overflow-auto">
                <?= TaskListWidget::widget(['user' => $user, 'tasks' => $tasks]) ?>
            </div>
        </div>
    </div>
</div>