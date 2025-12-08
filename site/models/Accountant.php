<?php

namespace app\models;

use Yii;
use app\services\AuthService;

/**
 * This is the model class for table "accountant".
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $rule
 * @property string|null $lang
 * @property string|null $email
 * @property string|null $password
 * @property string|null $token
 * @property string|null $create_at
 * @property string|null $update_at
 *
 * @property AccountantAccountantActivity[] $accountantAccountantActivities
 * @property AccountantActivity[] $accountantActivities
 * @property Company[] $companies
 * @property CompanyAccountant[] $companyAccountants
 */
class Accountant extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
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
            [['firstname', 'lastname',], 'default', 'value' => null],
            [['rule'], 'default', 'value' => 'accountant'],
            [['firstname', 'lastname', 'rule'], 'string', 'max' => 32],
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
            // 'username' => 'Username',
            'rule' => 'Rule',
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

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = AuthService::encodePassword($password);
    }

    public function generateAccessToken()
    {
        $this->token = AuthService::generateAccessToken();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function validateAuthKey($authKey)
    {
        return $authKey === $this->token;
    }
}
