<?php

use app\services\DictionaryService;
use app\services\SvgService;

?>
<table id="reg-reminder-table" class="w-full table-auto">
    <thead>
        <tr class="border-t bg-muted/50">
            <th class="p-6 text-left text-sm font-semibold tracking-tight">ID</th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('company', $user->lang)  ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('reminderType', $user->lang)  ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('reminderDate', $user->lang) ?> 1</th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('reminderDate', $user->lang) ?> 2</th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('escalationDate', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('status', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('reminderText', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($reminders as $item) {
            $class = ['reg-reminder-btn'];
            if ($item['status'] == 'cancelled') {
                $class[] = 'cancelled-reminder';
            }
        ?>
            <tr class="<?= implode(' ', $class) ?>" data-item-id="<?= $item['id'] ?>">
                <td class="p-6 pt-0"><?= $item['id'] ?></td>
                <td class="p-6 pt-0"><?= $item['name'] ?></td>
                <td class="p-6 pt-0"><?= $item['type'] ?></td>
                <td class="reg-reminder-1 p-6 pt-0"><?= date('Y-m-d', strtotime($item['reminder_1_date'])) ?></td>
                <td class="reg-reminder-2 p-6 pt-0"><?= date('Y-m-d', strtotime($item['reminder_2_date'])) ?></td>
                <td class="reg-reminder-escalation p-6 pt-0"><?= $item['escalation_date'] ?></td>
                <td class="reg-reminder-status p-6 pt-0"><?= $item['status'] ?></td>
                <td class="reg-reminder-message p-6 pt-0"><?= $item['message'] ?></td>
                <td class="reg-reminder-activity p-6 pt-0" style="white-space: nowrap;">
                    <?php
                    if ($item['status'] !== 'cancelled') {
                    ?>
                        <!-- <button class="edit-reg-reminder-btn inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 mt-4" data-item-id="<?= $item['id'] ?>" title="<?= DictionaryService::getWord('edit', $user->lang) ?>">
                            <?= SvgService::svg('edit') ?>
                        </button> -->
                        <button class="cancel-reg-reminder-btn p-1 rounded-md bg-primary border border-input disabled:opacity-50 disabled:pointer-events-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-ring font-medium gap-2 h-9 hover:bg-accent hover:text-accent-foreground inline-flex items-center justify-center mt-4 px-3 ring-offset-background [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg]:size-4 text-sm text-primary-foreground transition-colors whitespace-nowrap" data-item-id="<?= $item['id'] ?>" title="<?= DictionaryService::getWord('delete', $user->lang) ?>">
                            <?= SvgService::svg('delete') ?>
                        </button>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>