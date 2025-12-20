<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "poreski_kalendar".
 *
 * @property int $id
 * @property string $input_date
 * @property string $notification_date
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
            [['input_date', 'notification_date', 'activity_type', 'activity_text'], 'required'],
            [['input_date', 'notification_date'], 'safe'],
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
            'notification_date' => 'Notification Date',
            'activity_type' => 'Activity Type',
            'activity_text' => 'Activity Text',
            'activity' => 'Activity',
        ];
    }

}
