<?php

use yii\db\Migration;

class m251221_141523_advanced_field_to_passed_by_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%passed_by}}', 'summary', $this->string(512)->null()->after('lang'));
        $this->addColumn('{{%passed_by}}', 'dialog', $this->text()->null()->after('summary'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%passed_by}}', 'dialog');
        $this->dropColumn('{{%passed_by}}', 'summary');
    }

}
