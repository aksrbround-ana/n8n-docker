<?php

use yii\db\Migration;

class m251209_090546_fix_content_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn("{{%documents}}","content");
        $this->addColumn("{{%documents}}","content", 'BYTEA');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%documents}}","content");
        $this->addColumn("{{%documents}}","content", $this->text());
    }

}
