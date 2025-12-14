<?php

use app\services\DictionaryService;
use app\components\TaskListWidget;
use app\components\DocListWidget;
use app\components\CompanyNotesWidget;
use app\components\CompanyChatWidget;
use app\components\MakeTelegramLinkWidget

/**
 * @var $company app\models\Company
 * @var $activity app\models\CompanyActivities
 * @var $customers app\models\Customer[]
 * @var $user app\models\Accountant
 * @var $taskCount int
 * @var $tasks app\models\Task[]
 * @var $tasksOverdue int
 */      
?>
<div class="p-6">
    <div class="space-y-6">
        <button class="back inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4">
                <path d="m12 19-7-7 7-7"></path>
                <path d="M19 12H5"></path>
            </svg><?= DictionaryService::getWord('companies', $user->lang) ?>
        </button>
        <div class="bg-card rounded-xl border p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-7 w-7 text-primary">
                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                            <path d="M10 6h4"></path>
                            <path d="M10 10h4"></path>
                            <path d="M10 14h4"></path>
                            <path d="M10 18h4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-heading font-bold"><?= $company['name'] ?></h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-success/10 text-success border-success/20">
                                <?php
                                $statusName = 'status' . ucfirst($company['status']);
                                echo DictionaryService::getWord($statusName, $user->lang);
                                ?>
                            </span>
                        </div>
                        <p class="text-muted-foreground mt-1"><?= DictionaryService::getWord('pib', $user->lang) ?>: <?= $company->pib ?></p>
                    </div>
                </div>
                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen h-4 w-4 mr-2">
                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                    </svg>Редактировать

                </button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-muted-foreground">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span class="text-sm">Белград</span>
                </div>
                <!-- <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-4 w-4 text-muted-foreground">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span class="text-sm">Милош Павлович</span>
                </div> -->
                <div class="flex items-center gap-2">
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-4 w-4 text-muted-foreground">
                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg> -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-muted-foreground">
                        <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path>
                        <path d="m21.854 2.147-10.94 10.939"></path>
                    </svg>
                    <?php
                    foreach ($customers as $customer) {
                    ?>
                        <span class="text-sm">
                            <?= MakeTelegramLinkWidget::widget(['username' => $customer['username']]) ?>
                        </span>
                    <?php
                    }
                    ?>
                </div>
                <!-- <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 text-muted-foreground">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <span class="text-sm">+381 11 456 7891</span>
                </div> -->
            </div>
        </div>
        <div dir="ltr" data-orientation="horizontal" class="space-y-4">
            <div role="tablist" aria-orientation="horizontal" class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground" tabindex="0" data-orientation="horizontal" style="outline: none;">
                <button id="company-overview" type="button" role="tab" aria-selected="true" aria-controls="rcompany-overview" data-state="active" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-4 w-4">
                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                        <path d="M10 6h4"></path>
                        <path d="M10 10h4"></path>
                        <path d="M10 14h4"></path>
                        <path d="M10 18h4"></path>
                    </svg><?= DictionaryService::getWord('overview', $user->lang) ?>
                </button>
                <button id="company-tasks" type="button" role="tab" aria-selected="false" aria-controls="rcompany-tasks" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-todo h-4 w-4">
                        <rect x="3" y="5" width="6" height="6" rx="1"></rect>
                        <path d="m3 17 2 2 4-4"></path>
                        <path d="M13 6h8"></path>
                        <path d="M13 12h8"></path>
                        <path d="M13 18h8"></path>
                    </svg><?= DictionaryService::getWord('tasks', $user->lang) ?> (<?= $taskCount ?>)
                </button>
                <button id="company-docs" type="button" role="tab" aria-selected="false" aria-controls="company-docs" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-4 w-4">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                        <path d="M10 9H8"></path>
                        <path d="M16 13H8"></path>
                        <path d="M16 17H8"></path>
                    </svg><?= DictionaryService::getWord('documents', $user->lang) ?> (<?= $company->getDocumentsNumber() ?>)
                </button>
                <button id="company-notes" type="button" role="tab" aria-selected="false" aria-controls="company-notes" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square h-4 w-4">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg><?= DictionaryService::getWord('notes', $user->lang) ?> (<?= $company->getNotesNumber() ?>) ?
                </button>
                <button id="company-chat" type="button" role="tab" aria-selected="false" aria-controls="company-chat" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-4 w-4">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg><?= DictionaryService::getWord('chat', $user->lang) ?>
                </button>
            </div>
            <div data-state="active" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-overview" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-card rounded-xl border p-5">
                        <h3 class="font-medium text-muted-foreground text-sm"><?= DictionaryService::getWord('sector', $user->lang) ?></h3>
                        <p class="mt-1 font-semibold"><?= $activity->name ?></p>
                    </div>
                    <!-- <div class="bg-card rounded-xl border p-5">
                        <h3 class="font-medium text-muted-foreground text-sm">Ответственный бухгалтер</h3>
                        <p class="mt-1 font-semibold">Ольга Сидорова</p>
                    </div> -->
                    <div class="bg-card rounded-xl border p-5">
                        <h3 class="font-medium text-muted-foreground text-sm"><?= DictionaryService::getWord('openTasks', $user->lang) ?></h3>
                        <p class="mt-1 font-semibold"><?= $taskCount ?>
                            <?php
                            if ($tasksOverdue) {
                            ?>
                                <span class="text-destructive ml-2">(<?= $tasksOverdue ?> <?= strtolower(DictionaryService::getWord('overdue', $user->lang)) ?>)</span>
                            <?php
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-tasks" id="company-content-tasks" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <div class="space-y-4">
                    <div class="border rounded-lg overflow-hidden">
                        <div class="relative w-full overflow-auto">
                            <?= TaskListWidget::widget(['user' => $user, 'company' => $company]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-docs" id="company-content-documents" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <div class="border rounded-lg overflow-hidden">
                    <div class="relative w-full overflow-auto">
                        <?= DocListWidget::widget(['user' => $user, 'company' => $company]); ?>
                    </div>
                </div>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-notes" id="company-content-notes" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <?= CompanyNotesWidget::widget(['user' => $user, 'company' => $company]); ?>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-chat" id="company-content-contacts" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <?= CompanyChatWidget::widget(['user' => $user, 'company' => $company]); ?>
            </div>
        </div>
    </div>
</div>