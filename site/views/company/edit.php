<?php

use app\components\ButtonBackWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="p-6">
    <?= ButtonBackWidget::widget(['user' => $user]) ?>
    <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('companyEditing', $user->lang) ?></h1>
    <div class="space-y-6">
        <table id="company-edit-table" class="w-full caption-bottom text-sm">
            <input type="hidden" name="id" value="<?= $company->id ?>" />
            <tbody class="">
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle "><input type="text" name="name" value="<?= $company->name ?>" class="rounded-md border border-input bg-background" style="width:100%;" /></td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyNameTg', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle "><input type="text" name="name_tg" value="<?= $company->name_tg ?>" class="rounded-md border border-input bg-background" style="width:100%;" /></td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyType', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <select name="type" class="w-full">
                            <?php foreach ($companyTypes as $type) : ?>
                                <option value="<?= $type->id ?>" <?= $company->type_id == $type->id ? 'selected' : '' ?>><?= $type->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('status', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <select name="status" class="w-full">
                            <?php foreach ($companyStatuses as $status) : ?>
                                <option value="<?= $status ?>" <?= $company->status == $status ? 'selected' : '' ?>><?= DictionaryService::getWord('status' . ucfirst($status), $user->lang) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('PDV', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <input type="checkbox" name="is_pdv" <?= $company->is_pdv ? 'checked="checked"' : '' ?> />
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('sector', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <select name="activity" class="w-full">
                            <?php foreach ($companySector as $sector) : ?>
                                <option value="<?= $sector->id ?>" <?= $company->activity_id == $sector->id ? 'selected' : '' ?>><?= ucfirst($sector->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('pib', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle "><input type="text" name="pib" value="<?= $company->pib ?>" class="rounded-md border border-input bg-background" /></td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('Test', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <div class="custom-select-wrapper">
                            <div class="custom-select">
                                <div class="custom-select-trigger">Выберите вариант...</div>
                                <div class="custom-options">
                                    <span class="custom-option" data-value="1">Вариант 1</span>
                                    <span class="custom-option" data-value="2">Вариант 2</span>
                                    <span class="custom-option" data-value="3">Вариант 3</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="my-select-value" id="real-input">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right">
                        <button id="company-save-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" data-id="<?= $company['id'] ?>">
                            <?= SvgService::svg('save') ?>
                            <?= DictionaryService::getWord('save', $user->lang) ?>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>