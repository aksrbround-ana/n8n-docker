<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accountant".
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $username
 * @property string|null $status
 *
 * @property AccountantAccountantActivity[] $accountantAccountantActivities
 * @property AccountantActivity[] $accountantActivities
 * @property Company[] $companies
 * @property CompanyAccountant[] $companyAccountants
 */
class Accountant extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accountant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'username'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['firstname', 'lastname', 'username', 'status'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'username' => 'Username',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[AccountantAccountantActivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountantAccountantActivities()
    {
        return $this->hasMany(AccountantAccountantActivity::class, ['accountant_id' => 'id']);
    }

    /**
     * Gets query for [[AccountantActivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountantActivities()
    {
        return $this->hasMany(AccountantActivity::class, ['id' => 'accountant_activity_id'])->viaTable('accountant_accountant_activity', ['accountant_id' => 'id']);
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['id' => 'company_id'])->viaTable('company_accountant', ['accountant_id' => 'id']);
    }

    /**
     * Gets query for [[CompanyAccountants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyAccountants()
    {
        return $this->hasMany(CompanyAccountant::class, ['accountant_id' => 'id']);
    }

}
