<?php

use yii\db\Migration;

class m251207_181047_document_status_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documents}}', 'status', $this->string(16)->notNull()->defaultValue('new')->after('mimetype'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%documents}}', 'status');
    }
}
