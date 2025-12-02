<?php

use yii\db\Migration;

class m251128_163609_add_vector_extension extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE EXTENSION IF NOT EXISTS vector;');

        $this->createTable('{{%documents}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'content' => $this->text(),
            'metadata' => 'JSONB',
            'embedding' => 'vector(1536)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%documents}}');
        $this->execute('DROP EXTENSION IF EXISTS vector;');
    }
}
