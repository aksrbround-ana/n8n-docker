<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telegram_messages}}`.
 */
class m260130_141949_create_telegram_messages_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%telegram_messages}}', [
            'id' => $this->primaryKey(),
            // Тип сообщения: incoming (из ТГ) или outgoing (с сайта)
            'message_type' => $this->string(20)->notNull(),
            // ID чата и пользователя (используем bigInteger для безопасности)
            'chat_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger(),
            // ID сообщения в самом Telegram (чтобы избежать дублей или для ответов)
            'message_id' => $this->bigInteger(),
            // Текст сообщения
            'response' => $this->text()->notNull(),
            // Время отправки
            'sent_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Индексы для быстрого поиска сообщений в конкретном чате
        $this->createIndex(
            'idx-telegram_messages-chat_id',
            '{{%telegram_messages}}',
            'chat_id'
        );

        // Индекс для фильтрации по типу (если сообщений станет очень много)
        $this->createIndex(
            'idx-telegram_messages-message_type',
            '{{%telegram_messages}}',
            'message_type'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%telegram_messages}}');
    }
}
