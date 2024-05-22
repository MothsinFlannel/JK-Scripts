<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sale".
 *
 * @property int $id
 * @property int $locationId
 * @property int $terminal
 * @property string $clerk
 * @property double $amount
 * @property string $createdAt
 *
 * @property Location $location
 */
class Sale extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sale';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'amount'], 'required'],
            [['locationId', 'terminal'], 'default', 'value' => null],
            [['locationId', 'terminal'], 'integer'],
            [['amount'], 'number'],
            [['createdAt'], 'safe'],
            [['clerk'], 'string', 'max' => 32],
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
            'terminal' => Yii::t('app', 'Terminal'),
            'clerk' => Yii::t('app', 'Clerk'),
            'amount' => Yii::t('app', 'Amount'),
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
     * @return SaleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SaleQuery(get_called_class());
    }
}
