<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "poreski_kalendar".
 *
 * @property int $id
 * @property string $input_date
 * @property string $reminder_1_date
 * @property string $reminder_2_date
 * @property string $escalation_date
 * @property string $target_month
 * @property string $activity_type
 * @property string $activity_text
 * @property string $activity
 */
class TaxCalendar extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poreski_kalendar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activity'], 'default', 'value' => ''],
            [['input_date', 'activity_type', 'activity_text'], 'required'],
            [['input_date', 'reminder_1_date', 'reminder_2_date', 'escalation_date', 'target_month'], 'safe'],
            [['activity_type'], 'string', 'max' => 256],
            [['activity_text'], 'string', 'max' => 1024],
            [['activity'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'input_date' => 'Input Date',
            'reminder_1_date' => 'First notification Date',
            'reminder_2_date' => 'Second notification Date',
            'escalation_date' => 'Escalation Date',
            'target_month' => 'Target Month',
            'activity_type' => 'Activity Type',
            'activity_text' => 'Activity Text',
            'activity' => 'Activity',
        ];
    }

}
