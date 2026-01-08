<?php

use app\components\ButtonBackWidget;
use app\models\Task;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="p-6">
    <?= ButtonBackWidget::widget(['user' => $user]) ?>
    <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('taskEditing', $user->lang) ?></h1>
    <div class="space-y-6">
        <table id="task-edit-table" class="w-full caption-bottom text-sm">
            <input type="hidden" name="id" value="<?= $task->id ?>" />
            <tbody class="">
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('taskCategory', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle "><input type="text" name="category" value="<?= $task->category ?>" class"rounded-md border border-input bg-background" class="w-full rounded-md border border-input bg-background" style="width:100%;" /></td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('taskDetails', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <textarea name="request" class="w-full rounded-md border border-input bg-background" style="border: solid 1px gray;"><?= $task->request ?></textarea>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('status', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <select name="status" class="w-full">
                            <option value=""><?= DictionaryService::getWord('choose', $user->lang) ?></option>
                            <?php foreach ($statuses as $status) : ?>
                                <option value="<?= $status ?>" <?= $task->status == $status ? 'selected' : '' ?>><?= Task::getAnyStatusText($status, $user->lang) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('priority', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <select name="priority" class="w-full">
                            <option value=""><?= DictionaryService::getWord('choose', $user->lang) ?></option>
                            <?php foreach ($priorities as $priority) : ?>
                                <option value="<?= $priority ?>" <?= $task->priority == $priority ? 'selected' : '' ?>><?= Task::getAnyPriorityText($priority, $user->lang) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('dueDate', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <input type="date" name="due_date" value="<?= $task->due_date ? explode(' ', $task->due_date)[0] : '' ?>" class="rounded-md border border-input bg-background" />
                        <input type="hidden" name="due_date_time" value="<?= $task->due_date ?>" />
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('company', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <select name="company" class="w-full">
                            <option value=""><?= DictionaryService::getWord('choose', $user->lang) ?></option>
                            <?php foreach ($companies as $company) : ?>
                                <option value="<?= $company->id ?>" <?= $task->company_id == $company->id ? 'selected' : '' ?>><?= $company->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('accountant', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <select name="accountant" class="w-full">
                            <option value=""><?= DictionaryService::getWord('choose', $user->lang) ?></option>
                            <?php foreach ($accountants as $accountant) : ?>
                                <option value="<?= $accountant->id ?>" <?= $task->accountant_id === $accountant->id ? 'selected' : '' ?>><?= $accountant->firstname . ' ' . $accountant->lastname ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right">
                        <button id="task-save-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" data-id="<?= $task['id'] ?>">
                            <?= SvgService::svg('save') ?>
                            <?= DictionaryService::getWord('save', $user->lang) ?>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>