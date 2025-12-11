<?php

use app\services\DictionaryService;

/** @var app\models\Task $task */
/** @var app\models\Accountant $user */
?>
<div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
        <button class="back-to-tasklist inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-10 w-10">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-5 w-5">
                <path d="m12 19-7-7 7-7"></path>
                <path d="M19 12H5"></path>
            </svg>
        </button>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold"><?= $task->category ?></h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-destructive/10 text-destructive border-destructive/20"><?= $task->getStatusText($user->lang) ?></span>
                <?php
                $priorityWord = $task->getPriorityWord();
                $prioritySign = DictionaryService::$prioritySign[$task->priority];
                ?>
                <span class="inline-flex items-center gap-1 text-xs font-medium text-destructive">
                    <span>
                        <?= $prioritySign ?>
                    </span><?= DictionaryService::getWord($priorityWord, $user->lang) ?>
                </span>
            </div>
            <p class="text-muted-foreground"><?= $task->request ?></p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen h-4 w-4 mr-2">
                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
            </svg>
            <?= DictionaryService::getWord('edit', $user->lang) ?>
        </button>
        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-4 w-4 mr-2">
                <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                <path d="m9 11 3 3L22 4"></path>
            </svg>
            <?= DictionaryService::getWord('finish', $user->lang) ?>
        </button>
    </div>
</div>