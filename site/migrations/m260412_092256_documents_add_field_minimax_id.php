<?php

use yii\db\Migration;

class m260412_092256_documents_add_field_minimax_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documents}}', 'minimax_id', $this->integer()->null()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%documents}}', 'minimax_id');
    }
}
