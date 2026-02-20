<?php

use app\models\Document;
use app\services\DictionaryService;
use app\services\SvgService;
use Codeception\Lib\Di;

/**
 * @var $user app\model\Accountant
 * @var $docs [app\model\Document]
 * @var $doc app\model\Document
 */

?>

<table class="w-full caption-bottom text-sm">
    <thead class="[&amp;_tr]:border-b bg-secondary/50">
        <tr class="border-b transition-colors hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('fileName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('documentType', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('documentStatus', $user->lang) ?></th>
        </tr>
    </thead>
    <tbody class="[&amp;_tr:last-child]:border-0">
        <?php
        foreach ($docs as $doc) {
        $class = $doc->status == Document::STATUS_ARCHIVED ? 'doc-no-open' : 'doc-row cursor-pointer';
        ?>
            <tr data-doc-id="<?= $doc->id ?>" class="<?= $class ?> border-b hover:bg-secondary/30 transition-colors">
                <td class="p-4 align-middle">
                    <div class="flex items-center gap-2">
                        <?= SvgService::svg('document') ?>
                        <div>
                            <p class="font-medium text-sm truncate max-w-[200px]"><?= $doc->filename ?></p>
                            <p class="text-xs text-muted-foreground"><?= Document::getStaticLength($doc->id) ?></p>
                        </div>
                    </div>
                </td>
                <td class="p-4 align-middle text-sm"><?= $doc->getCompany() ? $doc->getCompany()->name : DictionaryService::getWord('unknown', $user->lang) ?></td>
                <td class="p-4 align-middle text-sm"><?= DictionaryService::getWord('docType' . ucfirst($doc->getType()->name), $user->lang) ?></td>
                <td class="p-4 align-middle"><span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border <?= $doc->getStatusStyle() ?>"><?= DictionaryService::getWord($doc->getStatusName(), $user->lang) ?></span></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>