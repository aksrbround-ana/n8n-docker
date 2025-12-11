<?php

use app\services\DictionaryService;
use app\models\Document;

?>
<div class="p-6">
    <div class="space-y-6">
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
        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('taskDetails', $user->lang) ?></h3>
                    </div>
                    <div class="p-6 pt-0 space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-muted-foreground mb-1"><?= DictionaryService::getWord('description', $user->lang) ?></h4>
                            <p class="text-sm"><?= $task->request ?></p>
                        </div>
                        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- <div class="flex items-center gap-3">
                                <div class="p-2 bg-secondary rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 text-muted-foreground">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Период</p>
                                    <p class="text-sm font-medium">Октябрь 2024</p>
                                </div>
                            </div> -->
                            <?php
                            if ($task->due_date < date('Y-m-d')) {
                            ?>
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-destructive/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 text-destructive">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('dueDate', $user->lang) ?></p>
                                    <p class="text-sm font-medium text-destructive"><?= $task->due_date ?><span class="ml-2 text-xs">(<?= DictionaryService::getWord('overdue', $user->lang) ?>)</span></p>
                                </div>
                            </div>
                            <?php
                            } else {
                            ?>
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 text-muted-foreground">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('dueDate', $user->lang) ?></p>
                                    <p class="text-sm font-medium "><?= $task->due_date ?></p>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-secondary rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-4 w-4 text-muted-foreground">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('assignedTo', $user->lang) ?></p>
                                    <p class="text-sm font-medium"><?php $accountant = $task->getAccountant(); echo $accountant->firstname . ' ' . $accountant->lastname ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-secondary rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 text-muted-foreground">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('lastUpdate', $user->lang) ?></p>
                                    <p class="text-sm font-medium"><?= $task->update_at ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="space-y-1.5 p-6 flex flex-row items-center justify-between">
                        <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('linkedDocuments', $user->lang) ?></h3>
                        <!-- <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-4 w-4 mr-2">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                <path d="M10 9H8"></path>
                                <path d="M16 13H8"></path>
                                <path d="M16 17H8"></path>
                            </svg>
                            Добавить документ
                        </button> -->
                    </div>
                    <div class="p-6 pt-0">
                        <?php
                        $documents = $task->getDocuments();
                        if (!empty($documents)) {
                            foreach ($documents as $document) {
                        ?>
                        <div class="space-y-2">
                            <a class="document-view flex items-center justify-between p-3 rounded-lg border hover:bg-secondary/50 transition-colors" href="/document/view">
                                <input type="hidden" class="document" value="<?= $document->id ?>" />
                                <div class="flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 text-muted-foreground">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                        <path d="M10 9H8"></path>
                                        <path d="M16 13H8"></path>
                                        <path d="M16 17H8"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium"><?= $document->filename ?></p>
                                        <p class="text-xs text-muted-foreground"><?= Document::getLength($document->id) ?></p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border"></span>
                            </a>
                        </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h3 class="font-semibold tracking-tight text-lg flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square h-5 w-5">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            Комментарии (7)
                        </h3>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-medium">МИ</div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2"><span class="text-sm font-medium">Марина Иванова</span><span class="text-xs text-muted-foreground">2 дня назад</span></div>
                                    <p class="text-sm text-muted-foreground mt-1">Ожидаю документы от клиента для завершения задачи.</p>
                                </div>
                            </div>
                        </div>
                        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full my-4"></div>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Добавить комментарий..." class="flex-1 px-3 py-2 text-sm border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 w-10">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-4 w-4">
                                    <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path>
                                    <path d="m21.854 2.147-10.94 10.939"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h3 class="font-semibold tracking-tight text-lg flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-5 w-5">
                                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                                <path d="M10 6h4"></path>
                                <path d="M10 10h4"></path>
                                <path d="M10 14h4"></path>
                                <path d="M10 18h4"></path>
                            </svg>Информация о компании
                        </h3>
                    </div>
                    <div class="p-6 pt-0 space-y-3"><a class="text-lg font-semibold text-primary hover:underline" href="/companies/3">Restoran Balkan</a>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-muted-foreground">ПИБ:</span><span class="font-mono">334567890</span></div>
                            <div class="flex justify-between"><span class="text-muted-foreground">Город:</span><span>Белград</span></div>
                            <div class="flex justify-between"><span class="text-muted-foreground">Сектор:</span><span>Ресторанный бизнес</span></div>
                        </div>
                        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full"></div>
                        <div class="space-y-1">
                            <p class="text-sm font-medium">Драган Николич</p>
                            <p class="text-xs text-muted-foreground">dragan@balkan.rs</p>
                            <p class="text-xs text-muted-foreground">+381 11 234 5678</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h3 class="font-semibold tracking-tight text-lg">Активность</h3>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
                                <div>
                                    <p class="text-sm">Задача создана</p>
                                    <p class="text-xs text-muted-foreground">20 ноября 2024 г. в 16:30</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>