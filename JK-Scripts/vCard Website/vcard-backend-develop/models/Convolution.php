<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "convolution".
 *
 * @property int $id
 * @property int $locationId
 * @property int $terminal
 * @property string $date
 * @property double $moneyIn
 * @property double $moneyOut
 * @property double $percentageProfit
 * @property double $flatFee
 * @property bool $isLive
 * @property string $lastLogAt
 *
 * @property Location $location
 */
class Convolution extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'convolution';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'terminal', 'date', 'moneyIn', 'moneyOut', 'percentageProfit'], 'required'],
            [['locationId', 'terminal'], 'default', 'value' => null],
            [['locationId', 'terminal'], 'integer'],
            [['date', 'lastLogAt'], 'safe'],
            [['moneyIn', 'moneyOut', 'percentageProfit', 'flatFee'], 'number'],
            [['isLive'], 'boolean'],
            [['locationId', 'date', 'terminal', 'isLive'], 'unique', 'targetAttribute' => ['locationId', 'date', 'terminal', 'isLive']],
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
            'date' => Yii::t('app', 'Date'),
            'moneyIn' => Yii::t('app', 'Money In'),
            'moneyOut' => Yii::t('app', 'Money Out'),
            'percentageProfit' => Yii::t('app', 'Percentage Profit'),
            'flatFee' => Yii::t('app', 'Flat Fee'),
            'isLive' => Yii::t('app', 'Is Live'),
            'lastLogAt' => Yii::t('app', 'Last Log At'),
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
     * @return ConvolutionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConvolutionQuery(get_called_class());
    }
}
