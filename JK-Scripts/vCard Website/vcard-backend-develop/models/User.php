<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $accessToken
 * @property string $email
 * @property string $fullName
 * @property string $phone
 * @property string $role
 * @property bool $isActive
 * @property string $createdAt
 * @property string $password
 * @property string $oid
 * @property string $recoveryToken
 * @property int $companyId
 * @property array $operatesInStates
 * @property string $passwordExpiresAt
 *
 * @property Access[] $accesses
 * @property Job[] $jobs
 * @property Staff[] $staff
 * @property Location[] $locations
 * @property Task[] $tasks
 * @property Company $company
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accessToken', 'email', 'fullName', 'role'], 'required'],
            [['role'], 'string'],
            [['isActive'], 'boolean'],
            [['createdAt', 'operatesInStates', 'passwordExpiresAt'], 'safe'],
            [['companyId'], 'default', 'value' => null],
            [['companyId'], 'integer'],
            [['accessToken'], 'string', 'max' => 32],
            [['email', 'fullName', 'phone'], 'string', 'max' => 128],
            [['password', 'recoveryToken'], 'string', 'max' => 64],
            [['oid'], 'string', 'max' => 24],
            [['recoveryToken'], 'unique'],
            [['email'], 'unique'],
            [['oid'], 'unique'],
            [['companyId'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['companyId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'accessToken' => Yii::t('app', 'Access Token'),
            'email' => Yii::t('app', 'Email'),
            'fullName' => Yii::t('app', 'Full Name'),
            'phone' => Yii::t('app', 'Phone'),
            'role' => Yii::t('app', 'Role'),
            'isActive' => Yii::t('app', 'Is Active'),
            'createdAt' => Yii::t('app', 'Created At'),
            'password' => Yii::t('app', 'Password'),
            'oid' => Yii::t('app', 'Oid'),
            'recoveryToken' => Yii::t('app', 'Recovery Token'),
            'companyId' => Yii::t('app', 'Company ID'),
            'operatesInStates' => Yii::t('app', 'Operates In States'),
            'passwordExpiresAt' => Yii::t('app', 'Password Expires At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccesses()
    {
        return $this->hasMany(Access::className(), ['userId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobs()
    {
        return $this->hasMany(Job::className(), ['initiatorId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasMany(Staff::className(), ['userId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['id' => 'locationId'])->viaTable('staff', ['userId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['authorId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'companyId']);
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
