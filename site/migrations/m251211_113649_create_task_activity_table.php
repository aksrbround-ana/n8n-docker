<?php

use yii\db\Migration;
use app\models\Accountant;
use app\models\Task;
use app\models\TaskActivity;

/**
 * Handles the creation of table `{{%task_activity}}`.
 */
class m251211_113649_create_task_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%task_step}}", [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->insert('{{%task_step}}', ['name' => 'created']);
        $this->insert('{{%task_step}}', ['name' => 'assigned']);
        $this->insert('{{%task_step}}', ['name' => 'in_progress']);
        $this->insert('{{%task_step}}', ['name' => 'waiting']);
        $this->insert('{{%task_step}}', ['name' => 'overdue']);
        $this->insert('{{%task_step}}', ['name' => 'done']);
        $this->insert('{{%task_step}}', ['name' => 'closed']);
        $this->insert('{{%task_step}}', ['name' => 'archived']);
        $this->insert('{{%task_step}}', ['name' => 'priority_changed']);
        $this->insert('{{%task_step}}', ['name' => 'due_date_changed']);

        $this->createTable('{{%task_activity}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->bigInteger()->notNull(),
            'accountant_id' => $this->bigInteger()->notNull(),
            'step_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_task_activity_task_id', '{{%task_activity}}', 'task_id');
        $this->createIndex('idx_task_activity_accountant_id', '{{%task_activity}}', 'accountant_id');
        $this->addForeignKey(
            'fk_task_activity_task_id',
            TaskActivity::tableName(),
            'task_id',
            Task::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_task_activity_accountant_id',
            TaskActivity::tableName(),
            'accountant_id',
            Accountant::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_task_activity_step_id',
            TaskActivity::tableName(),
            'step_id',
            '{{%task_step}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_task_activity_task_id', '{{%task_activity}}');
        $this->dropForeignKey('fk_task_activity_accountant_id', '{{%task_activity}}');
        $this->dropForeignKey('fk_task_activity_step_id', '{{%task_activity}}');
        $this->dropIndex('idx_task_activity_task_id', '{{%task_activity}}');
        $this->dropIndex('idx_task_activity_accountant_id', '{{%task_activity}}');
        $this->dropTable('{{%task_step}}');
        $this->dropTable('{{%task_activity}}');
    }
}
