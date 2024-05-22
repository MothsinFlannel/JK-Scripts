<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "redemption".
 *
 * @property int $id
 * @property int $locationId
 * @property string $clerk
 * @property int $terminal
 * @property int $replayTerminal
 * @property double $totalAmount
 * @property double $replayAmount
 * @property double $redemptionAmount
 * @property string $createdAt
 *
 * @property Location $location
 * @property RedemptionItem[] $redemptionItems
 */
class Redemption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'redemption';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'totalAmount', 'createdAt'], 'required'],
            [['locationId', 'terminal', 'replayTerminal'], 'default', 'value' => null],
            [['locationId', 'terminal', 'replayTerminal'], 'integer'],
            [['totalAmount', 'replayAmount', 'redemptionAmount'], 'number'],
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
            'clerk' => Yii::t('app', 'Clerk'),
            'terminal' => Yii::t('app', 'Terminal'),
            'replayTerminal' => Yii::t('app', 'Replay Terminal'),
            'totalAmount' => Yii::t('app', 'Total Amount'),
            'replayAmount' => Yii::t('app', 'Replay Amount'),
            'redemptionAmount' => Yii::t('app', 'Redemption Amount'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getRedemptionItems()
    {
        return $this->hasMany(RedemptionItem::className(), ['redemptionId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return RedemptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RedemptionQuery(get_called_class());
    }
}
