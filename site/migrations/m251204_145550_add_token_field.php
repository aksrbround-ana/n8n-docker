<?php

use yii\db\Migration;

class m251204_145550_add_token_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%accountant}}', 'token', $this->string(32)->notNull()->defaultValue('')->after('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%accountant}}', 'token');
    }

}
