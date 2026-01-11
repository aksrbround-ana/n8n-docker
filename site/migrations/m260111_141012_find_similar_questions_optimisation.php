<?php

use yii\db\Migration;

class m260111_141012_find_similar_questions_optimisation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
CREATE OR REPLACE FUNCTION public.find_similar_questions(
    query_embedding vector, 
    threshold double precision DEFAULT 0.8, 
    max_results integer DEFAULT 10
)
RETURNS TABLE(id integer, question_ru text, answer_ru text, question_rs text, answer_rs text, similarity double precision)
LANGUAGE plpgsql
AS \$function$
BEGIN
    RETURN QUERY
    SELECT 
        f.id,
        f.question_ru,
        f.answer_ru,
        f.question_rs,
        f.answer_rs,
        (1 - (f.embedding <=> query_embedding))::FLOAT AS similarity
    FROM {{%faq}} f
    WHERE f.status = 'approved'
      -- Используем чистый оператор расстояния. 
      -- Если сходство > 0.8, то расстояние < 0.2 (1 - 0.8)
      AND (f.embedding <=> query_embedding) < (1 - threshold)
    ORDER BY f.embedding <=> query_embedding
    LIMIT max_results;
END;
\$function$;
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
CREATE OR REPLACE FUNCTION find_similar_questions(
    query_embedding vector(768),
    threshold FLOAT DEFAULT 0.8,
    max_results INTEGER DEFAULT 10
)
RETURNS TABLE (
    id INTEGER,
    question_ru TEXT,
    answer_ru TEXT,
    question_rs TEXT,
    answer_rs TEXT,
    similarity FLOAT
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        f.id,
        f.question_ru,
        f.answer_ru,
        f.question_rs,
        f.answer_rs,
        (1 - (f.embedding <=> query_embedding))::FLOAT AS similarity
    FROM {{%faq}} f
    WHERE f.status = 'approved'
        AND 1 - (f.embedding <=> query_embedding) > threshold
    ORDER BY f.embedding <=> query_embedding
    LIMIT max_results;
END;
$$ LANGUAGE plpgsql;
        ");
    }
}
