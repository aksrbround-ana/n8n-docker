<?php
use app\services\DictionaryService;
?>
<div class="bg-card rounded-xl border p-5">
    <h3 class="font-heading font-semibold text-lg mb-4 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-5 w-5 text-primary">
            <path d="M8 2v4"></path>
            <path d="M16 2v4"></path>
            <rect width="18" height="18" x="3" y="4" rx="2"></rect>
            <path d="M3 10h18"></path>
        </svg>
        <?= DictionaryService::getWord('upcomingDeadlines', $user->lang) ?>
    </h3>
    <div class="space-y-3">
        <?php

        foreach ($data['upcomingDeadlines'] as $deadline) {
            $prioritySign = DictionaryService::$prioritySign[$deadline['priority']];
            $priorityWord = DictionaryService::getWord('priority' . ucfirst($deadline['priority']), $user->lang);
            if ($deadline['status'] === 'new') {
                $statusName = 'taskStatusNew';
            } elseif ($deadline['status'] === 'done') {
                $statusName = 'taskStatusDone';
            } elseif ($deadline['status'] === 'waiting') {
                $statusName = 'taskStatusWaiting';
            } elseif ($deadline['status'] === 'inProgress') {
                $statusName = 'taskStatusInProgress';
            } elseif ($deadline['status'] === 'overdue') {
                $statusName = 'taskStatusOverdue';
            }
            if ($deadline['time_left'] < 0) {
                $statusName = 'taskStatusOverdue';
            }
        ?>
            <div class="flex items-center justify-between p-3 rounded-lg border transition-colors hover:bg-secondary/50 cursor-pointer border-destructive/30 bg-destructive/5">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-muted-foreground"><!--T-007--></span>
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-destructive">
                            <span><?= $prioritySign ?></span><?= $priorityWord ?> <?= $deadline['due_date'] ?>
                        </span>
                    </div>
                    <p class="font-medium text-sm mt-1 truncate"><?= $deadline['company_name'] ?></p>
                    <p class="text-xs text-muted-foreground truncate"><?= $deadline['request'] ?></p>
                </div>
                <div class="text-right ml-3 flex-shrink-0">
                    <?php
                    if ($deadline['time_left'] < 0) {
                    ?>
                        <p class="text-sm font-medium flex items-center gap-1 justify-end text-destructive"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-3.5 w-3.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" x2="12" y1="8" y2="12"></line>
                                <line x1="12" x2="12.01" y1="16" y2="16"></line>
                            </svg><?= -$deadline['time_left'] ?> <?= DictionaryService::getWord('daysOverdue', $user->lang) ?></p>
                    <?php
                    }
                    ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-destructive/10 text-destructive border-destructive/20 mt-1"><?= DictionaryService::getWord($statusName, $user->lang) ?></span>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>