<?php
use app\services\DictionaryService;
?>
<input type="hidden" name="reminderId" value="<?= $reminder->id ?>" />
<table class="w-full table-auto">
    <tr class="border-t bg-muted/50">        
        <th class="p-6 text-left text-sm font-semibold tracking-tight" width="20%"><?= DictionaryService::getWord('deadlineDay', $user->lang) ?>:</th>
        <td class="p-6 pt-0"><input class="border" style="width: 100%;" name="deadlineDay" type="text" value="<?= $reminder->deadline_day ?>" /></td>
    </tr>       
    <tr class="border-t bg-muted/50">
        <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('type_ru', $user->lang) ?>:</th>
        <td class="p-6 pt-0"><input class="border" style="width: 100%;"  name="type_ru" type="text" value="<?= $reminder->type_ru ?>" /></td>
    </tr>
    <tr class="border-t bg-muted/50">
        <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('type_rs', $user->lang) ?>:</th>
        <td class="p-6 pt-0"><input class="border" style="width: 100%;" name="type_rs" type="text" value="<?= $reminder->type_rs ?>" /></td>
    </tr>
    <tr class="border-t bg-muted/50">
        <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('text_ru', $user->lang) ?>:</th>
        <td class="p-6 pt-0"><input class="border" style="width: 100%;" name="text_ru" type="text" value="<?= $reminder->text_ru ?>" /></td>
    </tr>
    <tr class="border-t bg-muted/50">
        <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('text_rs', $user->lang) ?>:</th>
        <td class="p-6 pt-0"><input class="border" style="width: 100%;" name="text_rs" type="text" value="<?= $reminder->text_rs ?>" /></td>
    </tr>
</table>