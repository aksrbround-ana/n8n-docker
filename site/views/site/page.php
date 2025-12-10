<?php

use app\models\Accountant;
use app\services\AuthService;
use app\services\DictionaryService;

/** @var $user app\models\Accountant */
/** @var $data array */

?>
<div class="p-6">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('welcomeBack', $user->lang) ?>, <?= $user->firstname . ' ' . $user->lastname ?>!</h1>
            <p class="text-muted-foreground mt-1">Обзор</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('totalClients', $user->lang) ?></p>
                        <p class="mt-2 text-3xl font-bold font-heading"><?= $data['clents'] ?></p>
                    </div>
                    <div class="rounded-lg p-2.5 bg-primary/10 text-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-5 w-5">
                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                            <path d="M10 6h4"></path>
                            <path d="M10 10h4"></path>
                            <path d="M10 14h4"></path>
                            <path d="M10 18h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('activeTasks', $user->lang) ?></p>
                        <p class="mt-2 text-3xl font-bold font-heading"><?= $data['activeTasks'] ?></p>
                    </div>
                    <div class="rounded-lg p-2.5 bg-primary/10 text-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-todo h-5 w-5">
                            <rect x="3" y="5" width="6" height="6" rx="1"></rect>
                            <path d="m3 17 2 2 4-4"></path>
                            <path d="M13 6h8"></path>
                            <path d="M13 12h8"></path>
                            <path d="M13 18h8"></path>
                        </svg></div>
                </div>
            </div>
            <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-destructive/5 border-destructive/20">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('overdueTasks', $user->lang) ?></p>
                        <p class="mt-2 text-3xl font-bold font-heading"><?= $data['overdueTasks'] ?></p>
                    </div>
                    <div class="rounded-lg p-2.5 bg-destructive/10 text-destructive"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-5 w-5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" x2="12" y1="8" y2="12"></line>
                            <line x1="12" x2="12.01" y1="16" y2="16"></line>
                        </svg></div>
                </div>
            </div>
            <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-warning/5 border-warning/20">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('pendingDocuments', $user->lang) ?></p>
                        <p class="mt-2 text-3xl font-bold font-heading"><?= $data['docsToCheck'] ?></p>
                    </div>
                    <div class="rounded-lg p-2.5 bg-warning/10 text-warning"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                            <path d="M10 9H8"></path>
                            <path d="M16 13H8"></path>
                            <path d="M16 17H8"></path>
                        </svg></div>
                </div>
            </div>
        </div>
        <?php
        $permissions = AuthService::getPermissions($user);
        $viewAccountants = array_key_exists('viewAccountants', $permissions);
        ?>

        <div class="grid grid-cols-1 lg:grid-cols-<?= $viewAccountants ? 2 : 1 ?> gap-6">
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
            <?php
            if ($viewAccountants) {
            ?>
                <div class="bg-card rounded-xl border p-5">
                    <h3 class="font-heading font-semibold text-lg mb-4 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-5 w-5 text-primary">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg><?= DictionaryService::getWord('workloadDistribution', $user->lang) ?></h3>
                    <div class="space-y-4">
                        <?php
                        $maxTasks = 0;
                        foreach ($data['accountants'] as $accountant) {
                            if ($maxTasks < $accountant['tasks']) {
                                $maxTasks = $accountant['tasks'];
                            }
                        }
                        $maxTasks = $maxTasks < 10 ? 10 : $maxTasks;
                        foreach ($data['accountants'] as $accountant) {
                            $percents = round($accountant['tasks'] / $maxTasks * 100, 2);
                        ?>
                            <div>
                                <div class="flex items-center justify-between mb-1.5"><span class="text-sm font-medium truncate pr-2"><?= $accountant['firstname'] . ' ' . $accountant['lastname'] ?></span>
                                    <div class="flex items-center gap-2 flex-shrink-0"><span class="text-sm text-muted-foreground"><?= $accountant['tasks'] ?></span>
                                        <?php
                                        if ($accountant['overdueTasks']) {
                                        ?>
                                            <span class="text-xs text-destructive font-medium">(<?= $accountant['overdueTasks'] ?> !)</span>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="h-2 bg-secondary rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all bg-destructive" style="width: <?= $percents ?>%;"></div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        <!-- <div>
                        <div class="flex items-center justify-between mb-1.5"><span class="text-sm font-medium truncate pr-2">Ольга Сидорова</span>
                            <div class="flex items-center gap-2 flex-shrink-0"><span class="text-sm text-muted-foreground">2</span></div>
                        </div>
                        <div class="h-2 bg-secondary rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all bg-primary" style="width: 50%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5"><span class="text-sm font-medium truncate pr-2">Елена Козлова</span>
                            <div class="flex items-center gap-2 flex-shrink-0"><span class="text-sm text-muted-foreground">1</span></div>
                        </div>
                        <div class="h-2 bg-secondary rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all bg-primary" style="width: 25%;"></div>
                        </div>
                    </div> -->
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>