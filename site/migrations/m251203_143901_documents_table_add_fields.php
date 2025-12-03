<?php

use yii\db\Migration;

class m251203_143901_documents_table_add_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documents}}', 'filename', $this->string(512)->null()->after('content'));
        $this->addColumn('{{%documents}}', 'mimetype', $this->string(64)->null()->after('filename'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%documents}}', 'mimetype');
        $this->dropColumn('{{%documents}}', 'filename');
    }

}
