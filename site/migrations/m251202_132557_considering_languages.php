<?php

use yii\db\Migration;

class m251202_132557_considering_languages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer', 'lang', $this->string(8)->notNull()->defaultValue('ru'));
        $this->addColumn('accountant', 'lang', $this->string(8)->notNull()->defaultValue('ru'));
        $this->dropColumn('accountant', 'username');
        $this->addColumn('accountant', 'email', $this->string(64)->notNull());
        $this->addColumn('accountant', 'password', $this->string(64)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer', 'lang');
        $this->dropColumn('accountant', 'lang');
        $this->dropColumn('accountant', 'email');
        $this->dropColumn('accountant', 'password');
        $this->addColumn('accountant', 'username', $this->string(32));
    }

}
