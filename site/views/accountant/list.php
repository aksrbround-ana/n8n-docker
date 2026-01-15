<?php

use app\components\ButtonBackWidget;
use app\services\DictionaryService;

?>
<div class="p-6">
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('accountants', $user->lang) ?></h1>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1 space-y-3">
                        <?php
                        foreach ($accountants as $accountant) {
                        ?>
                        <!-- <div class="rounded-lg text-card-foreground shadow-sm hover:underline text-sm font-medium cursor-pointer" data-v0-t="card"> -->
                            <div class="p-2">
                                <div class="load-by-link hover:underline cursor-pointer space-y-2" data-link="/accountant/page/<?= $accountant->id ?>" data-target="accountant-map"><?= $accountant->firstname . ' ' . $accountant->lastname ?></div>
                            </div>
                        <!-- </div> -->
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div>
                <div id="accountant-map" class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                </div>
            </div>
        </div>
    </div>
</div>