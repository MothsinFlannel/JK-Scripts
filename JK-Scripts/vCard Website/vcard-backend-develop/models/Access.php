<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access".
 *
 * @property int $id
 * @property int $userId
 * @property string $referenceType
 * @property int $referenceId
 * @property string $createdAt
 *
 * @property User $user
 */
class Access extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'referenceType', 'referenceId'], 'required'],
            [['userId', 'referenceId'], 'default', 'value' => null],
            [['userId', 'referenceId'], 'integer'],
            [['createdAt'], 'safe'],
            [['referenceType'], 'string', 'max' => 32],
            [['userId', 'referenceType', 'referenceId'], 'unique', 'targetAttribute' => ['userId', 'referenceType', 'referenceId']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'referenceType' => Yii::t('app', 'Reference Type'),
            'referenceId' => Yii::t('app', 'Reference ID'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
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
     * @return AccessQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AccessQuery(get_called_class());
    }
}
