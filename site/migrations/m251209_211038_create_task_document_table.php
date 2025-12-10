<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task_document}}`.
 */
class m251209_211038_create_task_document_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task_document}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'document_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey(
            'fk-task_document-task_id',
            '{{%task_document}}',
            'task_id',
            '{{%task}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-task_document-document_id',
            '{{%task_document}}',
            'document_id',
            '{{%documents}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-task_document-task_id', '{{%task_document}}');
        $this->dropForeignKey('fk-task_document-document_id', '{{%task_document}}');
        $this->dropTable('{{%task_document}}');
    }
}
