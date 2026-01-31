<?php

use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="bg-card rounded-xl border p-6">
    <div class="flex items-center gap-4 pb-4 border-b">
        <div class="chat chat-wrapper" data-chat-id="">
            <div class="chat-header">
                <?= DictionaryService::getWord('telegramChat', $user->lang) ?>
            </div>

            <div id="chat-display" class="chat-messages">
            </div>

            <div class="chat-input-area">
                <input type="hidden" id="chat-id" value="<?= $customer['tg_id'] ?>">
                <input type="text" id="chat-input" placeholder="<?= DictionaryService::getWord('writeMessage', $user->lang) ?>">
                <button id="send-message-button" class="font-medium text-sm inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    <?= SvgService::svg('telegram-white') ?>
                </button>
            </div>
        </div>
    </div>
</div>