<?php

use yii\db\Migration;

class m260211_160900_log_table_modify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('log', 'tg_id');
        $this->dropColumn('log', 'updated_at');
        $this->dropColumn('log', 'created_at');
        $this->addColumn('log', 'ai_text', $this->text()->after('message'));
        $this->addColumn('log', 'created_at', $this->integer()->after('ai_text'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('log', 'ai_text');
        $this->addColumn('log', 'tg_id', $this->bigInteger()->after('id'));
        $this->addColumn('log', 'updated_at', $this->integer()->after('created_at'));
    }
}
