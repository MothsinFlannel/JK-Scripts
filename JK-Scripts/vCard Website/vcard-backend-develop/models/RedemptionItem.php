<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "redemptionItem".
 *
 * @property int $id
 * @property int $redemptionId
 * @property string $category
 * @property double $amount
 *
 * @property Redemption $redemption
 */
class RedemptionItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'redemptionItem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['redemptionId', 'category', 'amount'], 'required'],
            [['redemptionId'], 'default', 'value' => null],
            [['redemptionId'], 'integer'],
            [['amount'], 'number'],
            [['category'], 'string', 'max' => 32],
            [['redemptionId'], 'exist', 'skipOnError' => true, 'targetClass' => Redemption::className(), 'targetAttribute' => ['redemptionId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'redemptionId' => Yii::t('app', 'Redemption ID'),
            'category' => Yii::t('app', 'Category'),
            'amount' => Yii::t('app', 'Amount'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRedemption()
    {
        return $this->hasOne(Redemption::className(), ['id' => 'redemptionId']);
    }

    /**
     * {@inheritdoc}
     * @return RedemptionItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RedemptionItemQuery(get_called_class());
    }
}
