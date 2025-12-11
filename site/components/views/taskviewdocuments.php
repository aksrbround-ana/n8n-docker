<?php

use app\services\DictionaryService;
use app\models\Document;
?>
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
                    <a class="document-view flex items-center justify-between p-3 rounded-lg border hover:bg-secondary/50 transition-colors" href="/document/view" data-document-id="<?= $document->id ?>">
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