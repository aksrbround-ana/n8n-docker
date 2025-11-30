<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int|null $company_id
 * @property string|null $category
 * @property string|null $request
 * @property int|null $due_date
 * @property string|null $status
 */
class Task extends \yii\db\ActiveRecord
{


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
            [['company_id', 'due_date'], 'integer'],
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

}
