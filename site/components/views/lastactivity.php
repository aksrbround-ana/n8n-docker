<?php

use app\services\DictionaryService;

?>
<div class="bg-card rounded-xl border p-5">
    <h3 class="font-heading font-semibold text-lg mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-5 w-5 text-primary">
            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
            <polyline points="16 7 22 7 22 13"></polyline>
        </svg>
        <?= DictionaryService::getWord('recentActivity', $user->lang) ?>
    </h3>
    <div class="space-y-3">
        <div class="flex items-center justify-between py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium">Задача выполнена</p>
                <p class="text-xs text-muted-foreground">НДС декларация - Global Trade SRB</p>
            </div><span class="text-xs text-muted-foreground">2ч назад</span>
        </div>
        <div class="flex items-center justify-between py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium">Документ загружен</p>
                <p class="text-xs text-muted-foreground">Банковская выписка - TechStart DOO</p>
            </div><span class="text-xs text-muted-foreground">4ч назад</span>
        </div>
        <div class="flex items-center justify-between py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium">Комментарий добавлен</p>
                <p class="text-xs text-muted-foreground">T-003 - Restoran Balkan</p>
            </div><span class="text-xs text-muted-foreground">5ч назад</span>
        </div>
        <div class="flex items-center justify-between py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium">Статус изменён</p>
                <p class="text-xs text-muted-foreground">T-005 - BuildPro</p>
            </div><span class="text-xs text-muted-foreground">Вчера</span>
        </div>
    </div>
</div>