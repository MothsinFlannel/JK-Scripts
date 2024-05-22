<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clerk".
 *
 * @property int $id
 * @property int $locationId
 * @property string $fullName
 * @property string $pin
 * @property bool $isManager
 * @property string $createdAt
 *
 * @property Location $location
 */
class Clerk extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clerk';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId'], 'default', 'value' => null],
            [['locationId'], 'integer'],
            [['fullName', 'pin'], 'required'],
            [['isManager'], 'boolean'],
            [['createdAt'], 'safe'],
            [['fullName'], 'string', 'max' => 32],
            [['pin'], 'string', 'max' => 16],
            [['locationId'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['locationId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'locationId' => Yii::t('app', 'Location ID'),
            'fullName' => Yii::t('app', 'Full Name'),
            'pin' => Yii::t('app', 'Pin'),
            'isManager' => Yii::t('app', 'Is Manager'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'locationId']);
    }

    /**
     * {@inheritdoc}
     * @return ClerkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClerkQuery(get_called_class());
    }
}
