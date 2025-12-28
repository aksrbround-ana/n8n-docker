<?php

use app\models\ReminderSchedule;
use yii\db\Migration;

class m251228_174428_add_field_to_reminder_scheduler extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(ReminderSchedule::tableName(), "attempts_made", $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(ReminderSchedule::tableName(), "attempts_made");
    }
}
