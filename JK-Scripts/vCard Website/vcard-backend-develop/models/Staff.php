<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "staff".
 *
 * @property int $locationId
 * @property int $userId
 *
 * @property Location $location
 * @property User $user
 */
class Staff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'staff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'userId'], 'required'],
            [['locationId', 'userId'], 'default', 'value' => null],
            [['locationId', 'userId'], 'integer'],
            [['locationId', 'userId'], 'unique', 'targetAttribute' => ['locationId', 'userId']],
            [['locationId'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['locationId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'locationId' => Yii::t('app', 'Location ID'),
            'userId' => Yii::t('app', 'User ID'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * {@inheritdoc}
     * @return StaffQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StaffQuery(get_called_class());
    }
}
