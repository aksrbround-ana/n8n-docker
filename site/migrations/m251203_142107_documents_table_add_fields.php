<?php

use yii\db\Migration;

class m251203_142107_documents_table_add_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documents}}', 'tg_id', $this->bigInteger()->null()->after('id'));
        $this->addColumn('{{%documents}}', 'company_id', $this->bigInteger()->null()->after('tg_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%documents}}', 'company_id');
        $this->dropColumn('{{%documents}}', 'tg_id');
    }
}
