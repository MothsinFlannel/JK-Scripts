<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string $name
 * @property string $contactName
 * @property string $contactEmail
 * @property bool $invoicingOnline
 * @property bool $isActive
 * @property string $createdAt
 * @property string $token
 * @property bool $isDefault
 *
 * @property Location[] $locations
 * @property User[] $users
 */
class Company extends \yii\db\ActiveRecord
{
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
            [['name'], 'required'],
            [['invoicingOnline', 'isActive', 'isDefault'], 'boolean'],
            [['createdAt'], 'safe'],
            [['name', 'contactName', 'contactEmail'], 'string', 'max' => 256],
            [['token'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'contactName' => Yii::t('app', 'Contact Name'),
            'contactEmail' => Yii::t('app', 'Contact Email'),
            'invoicingOnline' => Yii::t('app', 'Invoicing Online'),
            'isActive' => Yii::t('app', 'Is Active'),
            'createdAt' => Yii::t('app', 'Created At'),
            'token' => Yii::t('app', 'Token'),
            'isDefault' => Yii::t('app', 'Is Default'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['companyId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['companyId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CompanyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CompanyQuery(get_called_class());
    }
}
