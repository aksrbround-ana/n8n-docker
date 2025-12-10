<?php

namespace app\models;

use Yii;
use app\services\DictionaryService;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $accountant_id
 * @property string|null $category
 * @property string|null $request
 * @property int|null $due_date
 * @property string|null $status
 */
class Task extends \yii\db\ActiveRecord
{

    public $taskStatusStyles = [
        'overdue' =>   'bg-destructive/10 text-destructive border-destructive/20',
        'done' =>      'bg-success/10 text-success border-success/20',
        'waiting' =>   'bg-purple-100 text-purple-700 border-purple-200',
        'inProgress' => 'bg-warning/10 text-warning border-warning/20',
        'new' =>       'bg-info/10 text-info border-info/20',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'category', 'request', 'due_date'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['company_id', 'due_date'], 'default', 'value' => null],
            [['company_id', 'due_date', 'accountant_id'], 'integer'],
            [['category'], 'string', 'max' => 64],
            [['request'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'category' => 'Category',
            'request' => 'Request',
            'due_date' => 'Due Date',
            'status' => 'Status',
        ];
    }

    public function getAccountant()
    {
        return Accountant::findOne(['id' => $this->accountant_id]);
    }

    public function getCompany()
    {
        return Company::findOne(['id' => $this->company_id]);
    }

    public function getStatusStyle()
    {
        return $this->taskStatusStyles[$this->status] ?? '';
    }

    public function getPriorityWord()
    {
        return 'priority' . ucfirst($this->priority);
    }

    public function getStatusText($lang = 'ru')
    {
        return DictionaryService::getWord('taskStatus' . ucfirst($this->status), $lang);
    }

    public function getDocuments()
    {
        return TaskDocument::findAll(['task_id' => $this->id]);
    }
}
