<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $name_tg
 * @property int|null $type_id
 * @property bool|null $is_pdv
 * @property int|null $activity_id
 * @property string|null $specific_reports
 * @property int|null $report_date
 * @property string|null $reminder
 * @property int|null $pib
 * @property string|null $status
 *
 * @property Accountant[] $accountants
 * @property CompanyActivities $activity
 * @property CompanyAccountant[] $companyAccountants
 * @property CompanyType $type
 */
class Company extends \yii\db\ActiveRecord
{

    const COMPANY_STATUS_ACTIVE = 'active';
    const COMPANY_STATUS_ONBOARDING = 'onboarding';
    const COMPANY_STATUS_PAUSED = 'paused';
    const COMPANY_STATUS_INACTIVE = 'inactive';

    public static $statuses = [
        self::COMPANY_STATUS_ACTIVE,
        self::COMPANY_STATUS_ONBOARDING,
        self::COMPANY_STATUS_PAUSED,
        self::COMPANY_STATUS_INACTIVE,
    ];

    public static $types = [
        'DOO',
        'Knigaš',
        'Paušal,'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'name_tg', 'type_id', 'activity_id', 'specific_reports', 'report_date', 'reminder', 'pib'], 'default', 'value' => null],
            [['is_pdv'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 'onboarding'],
            [['type_id', 'activity_id', 'report_date', 'pib'], 'default', 'value' => null],
            [['type_id', 'activity_id', 'report_date', 'pib'], 'integer'],
            [['is_pdv'], 'boolean'],
            [['name', 'name_tg'], 'string', 'max' => 512],
            [['specific_reports', 'reminder'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 32],
            [['pib'], 'unique'],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompanyActivities::class, 'targetAttribute' => ['activity_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompanyType::class, 'targetAttribute' => ['type_id' => 'id']],
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
            'name_tg' => 'Name Tg',
            'type_id' => 'Type ID',
            'is_pdv' => 'Is Pdv',
            'activity_id' => 'Activity ID',
            'specific_reports' => 'Specific Reports',
            'report_date' => 'Report Date',
            'reminder' => 'Reminder',
            'pib' => 'Pib',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Accountants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountants()
    {
        return $this->hasMany(Accountant::class, ['id' => 'accountant_id'])->viaTable('company_accountant', ['company_id' => 'id']);
    }

    /**
     * Gets query for [[Activity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivityQuery()
    {
        return $this->hasOne(CompanyActivities::class, ['id' => 'activity_id']);
    }

    /**
     * Gets [[Activity]].
     *
     * @return CompanyActivities
     */
    public function getActivity()
    {
        return CompanyActivities::findOne($this->activity_id);
    }

    /**
     * Gets query for [[CompanyAccountants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyAccountants()
    {
        return $this->hasMany(CompanyAccountant::class, ['company_id' => 'id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(CompanyType::class, ['id' => 'type_id']);
    }

    public function getDocumentsNumber()
    {
        return Document::find()->where(['company_id' => $this->id])->count();
    }

    public function getNotesNumber()
    {
        return CompanyNotes::find()->where(['company_id' => $this->id, 'status' => 'active'])->count();
    }

    public function getCustomer()
    {
        return Customer::find()->where(['company_id' => $this->id, 'status' => 'active'])->one();
    }
}
