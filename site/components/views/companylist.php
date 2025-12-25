<?php

use app\services\DictionaryService;

foreach ($companies as $company) {
?>
    <div class="bg-card rounded-xl border hover:shadow-lg transition-all duration-200 overflow-hidden group">
        <div class="p-4 border-b bg-secondary/30">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="font-heading font-semibold text-base truncate group-hover:text-primary transition-colors"><?= $company['company_name'] ?></h3>
                    <p class="text-xs text-muted-foreground mt-0.5"><?= DictionaryService::getWord('pib', $user->lang) ?>: <?= $company['pib'] ?></p>
                    <?php
                    $statusName = 'status' . ucfirst($company['company_status']);
                    ?>
                </div>
                <?php
                if ($user->rule == 'ceo') {
                ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-success/10 text-success border-success/20"><?= DictionaryService::getWord($statusName, $user->lang) ?></span>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="p-4 space-y-3">
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2 text-muted-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 flex-shrink-0">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg><span class="truncate">Белград</span>
                </div>
                <div class="flex items-center gap-2 text-muted-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-4 w-4 flex-shrink-0">
                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                        <path d="M10 6h4"></path>
                        <path d="M10 10h4"></path>
                        <path d="M10 14h4"></path>
                        <path d="M10 18h4"></path>
                    </svg>
                    <span class="truncate"><?= $company['company_activity'] ?></span>
                </div>
                <!-- <div class="flex items-center gap-2 text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-4 w-4 flex-shrink-0">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="truncate">Ольга Сидорова</span>
                            </div> -->
            </div>
            <div class="flex items-center gap-4 pt-2 border-t">
                <div class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-todo h-4 w-4 text-info">
                        <rect x="3" y="5" width="6" height="6" rx="1"></rect>
                        <path d="m3 17 2 2 4-4"></path>
                        <path d="M13 6h8"></path>
                        <path d="M13 12h8"></path>
                        <path d="M13 18h8"></path>
                    </svg>
                    <span class="text-sm font-medium"><?= $company['openTasks'] ?></span>
                    <span class="text-xs text-muted-foreground"><?= DictionaryService::getWord('openTasks', $user->lang) ?></span>
                </div>
                <?php
                if ($company['overdueTasks']) {
                ?>
                    <div class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-4 w-4 text-destructive">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" x2="12" y1="8" y2="12"></line>
                            <line x1="12" x2="12.01" y1="16" y2="16"></line>
                        </svg>
                        <span class="text-sm font-medium text-destructive"><?= $company['overdueTasks'] ?></span>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="px-4 pb-4 flex gap-2">
            <button data-id="<?= $company['company_id'] ?>" class="company_open_profile inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3 flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link h-3.5 w-3.5 mr-1.5">
                    <path d="M15 3h6v6"></path>
                    <path d="M10 14 21 3"></path>
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                </svg>Открыть профиль
            </button>
            <!-- <button class="company_open_tasks inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                <input type="hidden" class="company" value="<?= $company['company_id'] ?>" />
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-todo h-3.5 w-3.5">
                    <rect x="3" y="5" width="6" height="6" rx="1"></rect>
                    <path d="m3 17 2 2 4-4"></path>
                    <path d="M13 6h8"></path>
                    <path d="M13 12h8"></path>
                    <path d="M13 18h8"></path>
                </svg>
            </button>
            <button class="company_open_docs inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                <input type="hidden" class="company" value="<?= $company['company_id'] ?>" />
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-3.5 w-3.5">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                    <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                    <path d="M10 9H8"></path>
                    <path d="M16 13H8"></path>
                    <path d="M16 17H8"></path>
                </svg>
            </button> -->
        </div>
    </div>
<?php
}
