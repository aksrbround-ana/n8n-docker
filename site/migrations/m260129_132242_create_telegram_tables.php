<?php

use yii\db\Migration;

/**
 * Class m260129_132242_create_telegram_tables
 */
class m260129_132242_create_telegram_tables extends Migration
{
    public function safeUp()
    {
        // 1. Таблица telegram_messages
        $this->createTable('{{%telegram_messages}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'username' => $this->string(255),
            'message_text' => $this->text()->notNull(),
            'message_type' => $this->string(50)->defaultValue('incoming'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // 2. Таблица operator_responses
        $this->createTable('{{%operator_responses}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->bigInteger()->notNull(),
            'operator_id' => $this->integer(),
            'operator_name' => $this->string(255),
            'response_text' => $this->text()->notNull(),
            'telegram_message_id' => $this->bigInteger(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'sent_at' => $this->timestamp(),
        ]);

        // 3. Индексы
        $this->createIndex('idx_telegram_messages_chat_id', '{{%telegram_messages}}', 'chat_id');
        $this->createIndex('idx_telegram_messages_created_at', '{{%telegram_messages}}', 'created_at');
        $this->createIndex('idx_operator_responses_chat_id', '{{%operator_responses}}', 'chat_id');
        $this->createIndex('idx_operator_responses_created_at', '{{%operator_responses}}', 'created_at');

        // 4. Функция и Триггер для updated_at (PostgreSQL specific)
        $this->execute("
            CREATE OR REPLACE FUNCTION update_tg_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        $this->execute("
            CREATE TRIGGER update_telegram_messages_updated_at
            BEFORE UPDATE ON {{%telegram_messages}}
            FOR EACH ROW
            EXECUTE FUNCTION update_tg_updated_at_column();
        ");
    }

    public function safeDown()
    {
        // Удаляем триггер и функцию
        $this->execute("DROP TRIGGER IF EXISTS update_telegram_messages_updated_at ON {{%telegram_messages}}");
        $this->execute("DROP FUNCTION IF EXISTS update_tg_updated_at_column()");

        // Удаляем таблицы (индексы удалятся автоматически)
        $this->dropTable('{{%operator_responses}}');
        $this->dropTable('{{%telegram_messages}}');
    }
}