<?php

use yii\db\Migration;
use app\models\ReminderSchedule;

class m260311_123431_add_button_field_to_reminder_scheduler extends Migration
{
    const BUTTON_PRESSED_COLUMN = 'done_by_user';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(ReminderSchedule::tableName(), self::BUTTON_PRESSED_COLUMN, $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(ReminderSchedule::tableName(), self::BUTTON_PRESSED_COLUMN);
    }
}
