<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_step".
 *
 * @property int $id
 * @property string $name
 * @property string|null $created_up
 * @property string|null $updated_up
 *
 * @property TaskActivity[] $taskActivities
 */
class TaskStep extends \yii\db\ActiveRecord
{

    public static $steps = [
        'taskStatusNew' =>  'taskStepCreated',
        'taskStatusInProgress' =>  'taskStepInProgress',
        'taskStatusWaiting' =>  'taskStepWaiting',
        'taskStatusDone' =>  'taskStepDone',
        'taskStatusOverdue' =>  'taskStepOverdue',
        'taskStatusClosed' =>  'taskStepClosed',
        'taskStatusArchived' =>  'taskStepArchived',

    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_step';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_up', 'updated_up'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_up' => 'Created Up',
            'updated_up' => 'Updated Up',
        ];
    }

    /**
     * Gets query for [[TaskActivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskActivities()
    {
        return $this->hasMany(TaskActivity::class, ['step_id' => 'id']);
    }
}
