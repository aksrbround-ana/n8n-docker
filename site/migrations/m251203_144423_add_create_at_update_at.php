<?php

use yii\db\Migration;

class m251203_144423_add_create_at_update_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%company}}', 'create_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%company}}', 'update_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%customer}}', 'create_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%customer}}', 'update_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%accountant}}', 'create_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%accountant}}', 'update_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%reminder}}', 'create_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%reminder}}', 'update_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%event}}', 'create_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%event}}', 'update_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%task}}', 'create_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%task}}', 'update_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%documents}}', 'create_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%documents}}', 'update_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%company}}', 'update_at');
        $this->dropColumn('{{%company}}', 'create_at');
        $this->dropColumn('{{%customer}}', 'update_at');
        $this->dropColumn('{{%customer}}', 'create_at');
        $this->dropColumn('{{%accountant}}', 'update_at');
        $this->dropColumn('{{%accountant}}', 'create_at');
        $this->dropColumn('{{%reminder}}', 'update_at');
        $this->dropColumn('{{%reminder}}', 'create_at');
        $this->dropColumn('{{%event}}', 'update_at');
        $this->dropColumn('{{%event}}', 'create_at');
        $this->dropColumn('{{%task}}', 'update_at');
        $this->dropColumn('{{%task}}', 'create_at');
        $this->dropColumn('{{%documents}}', 'update_at');
        $this->dropColumn('{{%documents}}', 'create_at');
    }

}
