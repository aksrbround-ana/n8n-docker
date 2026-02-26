<?php

use yii\db\Migration;

/**
 * Миграция для системы фонового обучения агента.
 * Реализует таблицы для отслеживания вопросов, обучения на ответах из чата,
 * а также вспомогательные View и хранимые функции.
 */
class m260226_110319_background_learning_schema extends Migration
{
    public function safeUp()
    {
        // 1. Таблица: user_questions_background
        $this->createTable('{{%user_questions_background}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'chat_id' => $this->bigInteger()->notNull(),
            'message_id' => $this->integer()->notNull(),
            'question_text' => $this->text()->notNull(),
            'language' => $this->string(5)->defaultValue('ru'),
            'agent_answered' => $this->boolean()->defaultValue(false),
            'awaiting_response' => $this->boolean()->defaultValue(true),
            'resolved_at' => $this->timestamp(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_uq_bg_created', '{{%user_questions_background}}', 'created_at');
        $this->createIndex('idx_uq_bg_awaiting', '{{%user_questions_background}}', 'awaiting_response');
        $this->createIndex('idx_uq_bg_user', '{{%user_questions_background}}', 'user_id');

        // 2. Таблица: faq_learned_from_chat
        // Примечание: тип 'vector(384)' требует расширения pgvector
        $this->createTable('{{%faq_learned_from_chat}}', [
            'id' => $this->primaryKey(),
            'user_question_id' => $this->integer()->notNull(),
            'question_text' => $this->text()->notNull(),
            'answer_text' => $this->text()->notNull(),
            'language' => $this->string(5),
            'answer_message_id' => $this->integer()->notNull(),
            'answered_by_user_id' => $this->bigInteger(),
            'answered_by_name' => $this->string(255),
            'duplicate_check_executed' => $this->boolean()->defaultValue(false),
            'duplicate_check_at' => $this->timestamp(),
            'is_duplicate' => $this->boolean()->defaultValue(false),
            'similarity_score' => $this->float(),
            'duplicate_faq_id' => $this->integer(),
            'added_to_faq' => $this->boolean()->defaultValue(false),
            'faq_entry_id' => $this->integer(),
            'embedding_ru' => 'vector(384)',
            'embedding_rs' => 'vector(384)',
            'status' => $this->string(30)->defaultValue('pending'),
            'processed' => $this->boolean()->defaultValue(false),
            'processed_at' => $this->timestamp(),
            'processing_notes' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk_faq_learned_user_question',
            '{{%faq_learned_from_chat}}',
            'user_question_id',
            '{{%user_questions_background}}',
            'id',
            'CASCADE'
        );

        // Индексы для faq_learned_from_chat
        $this->createIndex('idx_faq_learned_status', '{{%faq_learned_from_chat}}', 'status');
        $this->createIndex('idx_faq_learned_processed', '{{%faq_learned_from_chat}}', 'processed');
        $this->createIndex('idx_faq_learned_duplicate', '{{%faq_learned_from_chat}}', 'is_duplicate');
        $this->createIndex('idx_faq_learned_user_question', '{{%faq_learned_from_chat}}', 'user_question_id');
        $this->createIndex('idx_faq_learned_created', '{{%faq_learned_from_chat}}', 'created_at');

        // 3. Создание Views
        $this->execute("CREATE OR REPLACE VIEW unanswered_questions_waiting_response AS
            SELECT id, user_id, chat_id, question_text, language, created_at,
            EXTRACT(MINUTE FROM NOW() - created_at) as minutes_waiting
            FROM user_questions_background
            WHERE awaiting_response = TRUE AND agent_answered = FALSE
            ORDER BY created_at ASC;");

        $this->execute("CREATE OR REPLACE VIEW recently_learned_from_chat AS
            SELECT fl.id, fl.question_text, fl.answer_text, fl.language, fl.answered_by_name,
            fl.is_duplicate, fl.status, fl.similarity_score, fl.added_to_faq, fl.created_at, fl.processed_at
            FROM faq_learned_from_chat fl
            WHERE fl.processed = TRUE
            ORDER BY fl.created_at DESC
            LIMIT 100;");

        $this->execute("CREATE OR REPLACE VIEW background_learning_stats AS
            SELECT 
              COUNT(*) FILTER (WHERE processed = TRUE AND added_to_faq = TRUE) as total_added,
              COUNT(*) FILTER (WHERE processed = TRUE AND is_duplicate = TRUE) as total_duplicates,
              COUNT(*) FILTER (WHERE status = 'pending') as pending_processing,
              ROUND(
                100.0 * COUNT(*) FILTER (WHERE processed = TRUE AND added_to_faq = TRUE) / 
                NULLIF(COUNT(*) FILTER (WHERE processed = TRUE), 0), 1
              ) as success_rate_pct,
              COUNT(DISTINCT DATE(created_at)) as days_active
            FROM faq_learned_from_chat;");

        // 4. Создание функций (PL/pgSQL)

        // Функция проверки дублей
        $this->execute("CREATE OR REPLACE FUNCTION check_if_duplicate_question(p_embedding vector, p_language VARCHAR(5) DEFAULT 'ru') 
            RETURNS TABLE (is_duplicate BOOLEAN, faq_id INTEGER, question TEXT, similarity FLOAT) AS $$
            DECLARE
              v_max_similarity FLOAT := 0;
              v_faq_id INTEGER;
              v_question TEXT;
            BEGIN
              IF p_language = 'ru' THEN
                SELECT 1 - (f.embedding_ru <=> p_embedding), f.id, f.question_ru
                INTO v_max_similarity, v_faq_id, v_question
                FROM faq_entries f WHERE f.embedding_ru IS NOT NULL
                ORDER BY f.embedding_ru <=> p_embedding LIMIT 1;
              ELSE
                SELECT 1 - (f.embedding_rs <=> p_embedding), f.id, f.question_rs
                INTO v_max_similarity, v_faq_id, v_question
                FROM faq_entries f WHERE f.embedding_rs IS NOT NULL
                ORDER BY f.embedding_rs <=> p_embedding LIMIT 1;
              END IF;
              RETURN QUERY SELECT v_max_similarity > 0.85, v_faq_id, v_question, ROUND(v_max_similarity::numeric, 3)::FLOAT;
            END; $$ LANGUAGE plpgsql;");

        // Функция добавления пары в FAQ
        $this->execute("CREATE OR REPLACE FUNCTION add_learned_pair_to_faq(p_learned_id INTEGER, p_embedding_ru vector DEFAULT NULL, p_embedding_rs vector DEFAULT NULL) 
            RETURNS TABLE (success BOOLEAN, faq_entry_id INTEGER, message TEXT) AS $$
            DECLARE
              v_question_text TEXT; v_answer_text TEXT; v_language VARCHAR(5); v_new_faq_id INTEGER;
            BEGIN
              SELECT question_text, answer_text, language INTO v_question_text, v_answer_text, v_language
              FROM faq_learned_from_chat WHERE id = p_learned_id;
              IF v_question_text IS NULL THEN RETURN QUERY SELECT FALSE, NULL::INTEGER, 'Пара не найдена'::TEXT; RETURN; END IF;
              
              INSERT INTO faq_entries (question_ru, question_rs, answer_ru, answer_rs, embedding_ru, embedding_rs)
              VALUES (
                CASE WHEN v_language = 'ru' THEN v_question_text ELSE NULL END,
                CASE WHEN v_language = 'rs' THEN v_question_text ELSE NULL END,
                CASE WHEN v_language = 'ru' THEN v_answer_text ELSE NULL END,
                CASE WHEN v_language = 'rs' THEN v_answer_text ELSE NULL END,
                p_embedding_ru, p_embedding_rs
              ) ON CONFLICT DO NOTHING RETURNING faq_entries.id INTO v_new_faq_id;
              
              IF v_new_faq_id IS NULL THEN
                SELECT id INTO v_new_faq_id FROM faq_entries WHERE (question_ru = v_question_text OR question_rs = v_question_text) LIMIT 1;
              END IF;
              
              UPDATE faq_learned_from_chat SET status = 'added', processed = TRUE, processed_at = NOW(), added_to_faq = TRUE, faq_entry_id = v_new_faq_id WHERE id = p_learned_id;
              UPDATE user_questions_background SET awaiting_response = FALSE, resolved_at = NOW() WHERE id = (SELECT user_question_id FROM faq_learned_from_chat WHERE id = p_learned_id);
              RETURN QUERY SELECT TRUE, v_new_faq_id, 'Успешно добавлено'::TEXT;
            END; $$ LANGUAGE plpgsql;");

        // Функция пометки дубля
        $this->execute("CREATE OR REPLACE FUNCTION mark_as_duplicate(p_learned_id INTEGER, p_duplicate_faq_id INTEGER, p_similarity FLOAT) 
            RETURNS void AS $$
            BEGIN
              UPDATE faq_learned_from_chat SET status = 'duplicate', processed = TRUE, processed_at = NOW(), is_duplicate = TRUE, 
              duplicate_check_executed = TRUE, duplicate_check_at = NOW(), duplicate_faq_id = p_duplicate_faq_id, similarity_score = p_similarity,
              processing_notes = 'Найден дубль, similarity: ' || ROUND(p_similarity::numeric, 3)::text
              WHERE id = p_learned_id;
              UPDATE user_questions_background SET awaiting_response = FALSE, resolved_at = NOW()
              WHERE id = (SELECT user_question_id FROM faq_learned_from_chat WHERE id = p_learned_id);
            END; $$ LANGUAGE plpgsql;");

        // Функция отклонения
        $this->execute("CREATE OR REPLACE FUNCTION reject_learned_pair(p_learned_id INTEGER, p_reason TEXT) 
            RETURNS void AS $$
            BEGIN
              UPDATE faq_learned_from_chat SET status = 'rejected', processed = TRUE, processed_at = NOW(), processing_notes = p_reason WHERE id = p_learned_id;
              UPDATE user_questions_background SET awaiting_response = FALSE, resolved_at = NOW()
              WHERE id = (SELECT user_question_id FROM faq_learned_from_chat WHERE id = p_learned_id);
            END; $$ LANGUAGE plpgsql;");
    }

    public function safeDown()
    {
        // Удаляем функции
        $this->execute("DROP FUNCTION IF EXISTS reject_learned_pair(INTEGER, TEXT)");
        $this->execute("DROP FUNCTION IF EXISTS mark_as_duplicate(INTEGER, INTEGER, FLOAT)");
        $this->execute("DROP FUNCTION IF EXISTS add_learned_pair_to_faq(INTEGER, vector, vector)");
        $this->execute("DROP FUNCTION IF EXISTS check_if_duplicate_question(vector, VARCHAR)");

        // Удаляем Views
        $this->execute("DROP VIEW IF EXISTS background_learning_stats");
        $this->execute("DROP VIEW IF EXISTS recently_learned_from_chat");
        $this->execute("DROP VIEW IF EXISTS unanswered_questions_waiting_response");

        // Удаляем таблицы
        $this->dropTable('{{%faq_learned_from_chat}}');
        $this->dropTable('{{%user_questions_background}}');
    }
}
