<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $invoiceId
 * @property double $amount
 * @property string $paidOn
 * @property string $createdAt
 * @property string $notes
 *
 * @property Invoice $invoice
 */
class Payment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoiceId', 'amount'], 'required'],
            [['invoiceId'], 'default', 'value' => null],
            [['invoiceId'], 'integer'],
            [['amount'], 'number'],
            [['paidOn', 'createdAt'], 'safe'],
            [['notes'], 'string'],
            [['invoiceId'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoiceId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'invoiceId' => Yii::t('app', 'Invoice ID'),
            'amount' => Yii::t('app', 'Amount'),
            'paidOn' => Yii::t('app', 'Paid On'),
            'createdAt' => Yii::t('app', 'Created At'),
            'notes' => Yii::t('app', 'Notes'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId']);
    }

    /**
     * {@inheritdoc}
     * @return PaymentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentQuery(get_called_class());
    }
}
