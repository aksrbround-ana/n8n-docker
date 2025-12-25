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
                <h1 class="text-2xl font-heading font-bold"><?= $user->firstname . ' ' . $user->lastname ?></h1>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-3">
            <form method="post" action="/accountant/save/<?= $user->id ?>" onsubmit="return false;">
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight"><?= DictionaryService::getWord('email', $user->lang) ?></h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-2">
                                    <input type="text" class="w-full border rounded px-3 py-2" value="<?= $user->email ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight"><?= DictionaryService::getWord('password', $user->lang) ?></h3>
                            </div>
                            <div class="p-6">
                                <p class="whitespace-nowrap leading-none tracking-tight"><?= DictionaryService::getWord('oldPassword', $user->lang) ?></p>
                                <div class="space-y-2"><input type="password" name="old_password" class="w-full border rounded px-3 py-2" value="" placeholder="••••••"></div>
                                <p class="whitespace-nowrap leading-none tracking-tight"><?= DictionaryService::getWord('newPassword', $user->lang) ?></p>
                                <div class="space-y-2"><input type="password" name="new_password" class="w-full border rounded px-3 py-2" value="" placeholder="••••••"></div>
                                <p class="whitespace-nowrap leading-none tracking-tight"><?= DictionaryService::getWord('confirmPassword', $user->lang) ?></p>
                                <div class="space-y-2"><input type="password" name="confirm_password" class="w-full border rounded px-3 py-2" value="" placeholder="••••••"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <button type="button" id="save-user" class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight"><?= DictionaryService::getWord('save', $user->lang) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>