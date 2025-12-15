<?php

use yii\db\Migration;

class m251203_144423_add_create_at_update_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%company}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%company}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%customer}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%customer}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%accountant}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%accountant}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%reminder}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%reminder}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%event}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%event}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%task}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%task}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%documents}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%documents}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%company}}', 'updated_at');
        $this->dropColumn('{{%company}}', 'created_at');
        $this->dropColumn('{{%customer}}', 'updated_at');
        $this->dropColumn('{{%customer}}', 'created_at');
        $this->dropColumn('{{%accountant}}', 'updated_at');
        $this->dropColumn('{{%accountant}}', 'created_at');
        $this->dropColumn('{{%reminder}}', 'updated_at');
        $this->dropColumn('{{%reminder}}', 'created_at');
        $this->dropColumn('{{%event}}', 'updated_at');
        $this->dropColumn('{{%event}}', 'created_at');
        $this->dropColumn('{{%task}}', 'updated_at');
        $this->dropColumn('{{%task}}', 'created_at');
        $this->dropColumn('{{%documents}}', 'updated_at');
        $this->dropColumn('{{%documents}}', 'created_at');
    }

}
