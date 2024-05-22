<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notificationEmail".
 *
 * @property int $id
 * @property int $locationId
 * @property string $email
 * @property string $createdAt
 *
 * @property Location $location
 */
class NotificationEmail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificationEmail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'email'], 'required'],
            [['locationId'], 'default', 'value' => null],
            [['locationId'], 'integer'],
            [['createdAt'], 'safe'],
            [['email'], 'string', 'max' => 128],
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
            'email' => Yii::t('app', 'Email'),
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
     * @return NotificationEmailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationEmailQuery(get_called_class());
    }
}
