<?php

use yii\db\Migration;

class m251207_183445_fix_date_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%task}}', 'due_date');
        $this->addColumn('{{%task}}', 'due_date', $this->dateTime()->notNull());
        $this->dropColumn('{{%company}}', 'report_date');
        $this->addColumn('{{%company}}', 'report_date', $this->dateTime()->null());
        $this->dropColumn('{{%reminder}}', 'send_date');
        $this->addColumn('{{%reminder}}', 'send_date', $this->dateTime()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

}
