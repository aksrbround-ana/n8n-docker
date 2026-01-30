<?php

use app\models\CompanyCustomer;
use app\models\Customer;
use app\services\SvgService;

$customer = Customer::find()
    ->innerJoin(CompanyCustomer::tableName(), Customer::tableName() . '.id=' . CompanyCustomer::tableName() . '.customer_id')
    ->where([CompanyCustomer::tableName() . '.company_id' => $company->id])
    ->limit(1)
    ->one();
?>
<div class="bg-card rounded-xl border p-6">
    <div class="flex items-center pb-4 border-b">
        <div class="chat">
            <input type="hidden" name="chat_id" value="<?= $customer->tg_id ?>" />
            <div class="sidebar">
                <div class="sidebar-header"><?= $customer->username ?></div>
            </div>
            <div class="messages-container" id="messagesContainer">
                <div class="chat-list" id="chatList" data-chat-id="<?= $customer->tg_id ?>"></div>
                <!-- <div class=""><?= $customer->username ?></div> -->
            </div>
            <div class="message-input-container" id="inputContainer">
                <input type="text" class="message-input" id="messageInput" placeholder="Введите сообщение...">
                <button class="send-button" id="sendButton">
                    <?= SvgService::svg('telegram-white') ?>
                    <!-- Отправить -->
                </button>
            </div>
        </div>
    </div>
</div>