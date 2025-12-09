<?php

use app\services\DictionaryService;
?>
<table class="w-full caption-bottom text-sm">
    <thead class="[&amp;_tr]:border-b bg-secondary/50 sticky top-0">
        <tr class="border-b transition-colors data-[state=selected]:bg-muted hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0 w-12"><button type="button" role="checkbox" aria-checked="false" data-state="unchecked" value="on" class="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></button></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0 w-20">ID</th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('taskType', $user->lang) ?></th>
            <!-- <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('period', $user->lang) ?></th> -->
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('status', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('priority', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('dueDate', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('assignedTo', $user->lang) ?></th>
            <!-- <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('lastUpdate', $user->lang) ?></th> -->
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0 w-12"></th>
        </tr>
    </thead>
    <tbody class="[&amp;_tr:last-child]:border-0">

        <?php
        foreach ($tasks as $task) {
        ?>
            <tr class="border-b data-[state=selected]:bg-muted cursor-pointer hover:bg-secondary/50 transition-colors<?php if ($task->status == 'done') echo ' bg-destructive/5'; ?>">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0"><button type="button" role="checkbox" aria-checked="false" data-state="unchecked" value="on" class="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></button></td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 font-mono text-xs"><?= $company->id ?></td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                    <div>
                        <p class="font-medium text-sm"><?= $company->name ?></p>
                        <p class="text-xs text-muted-foreground"><?= $company->pib ?></p>
                    </div>
                </td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm"><?= $task->category ?></td>
                <!-- <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm text-muted-foreground">2024</td> -->
                <?php
                $statusWord = 'taskStatus' . ucfirst($task->status);
                ?>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0"><span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-warning/10 text-warning border-warning/20"><?= DictionaryService::getWord($statusWord, $user->lang) ?></span></td>
                <?php
                $priorityWord = 'priority' . ucfirst($task->priority);
                ?>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0"><span class="inline-flex items-center gap-1 text-xs font-medium text-destructive"><span>↑</span><?= DictionaryService::getWord($priorityWord, $user->lang) ?></span></td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                    <?php
                    if ($task->due_date > date('Y-m-d')) {
                    ?>
                        <div class="flex items-center gap-1 text-sm"><?= $task->due_date ?></div>
                    <?php
                    } else {
                    ?>
                        <div class="flex items-center gap-1 text-sm text-destructive font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-3.5 w-3.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" x2="12" y1="8" y2="12"></line>
                                <line x1="12" x2="12.01" y1="16" y2="16"></line>
                            </svg>
                            <?= $task->due_date ?>
                        </div>
                    <?php
                    }
                    $accountant = $task->getAccountant();
                    ?>
                </td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm"><?= $accountant->firstname . ' ' . $accountant->lastname ?></td>
                <!-- <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-xs text-muted-foreground">28.11, 08:45</td> -->
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                    <div class="flex items-center gap-1">
                        <span class="flex items-center gap-0.5 text-xs text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square h-3.5 w-3.5">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>2
                        </span>
                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8" type="button" id="radix-:r181:" aria-haspopup="menu" aria-expanded="false" data-state="closed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis h-4 w-4">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>