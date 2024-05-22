<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoiceItem".
 *
 * @property int $id
 * @property int $invoiceId
 * @property string $number
 * @property string $title
 * @property double $totalIn
 * @property double $totalOut
 * @property double $revenue
 * @property double $balance
 * @property string $type
 * @property string $notes
 * @property string $lastLogAt
 *
 * @property Invoice $invoice
 */
class InvoiceItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoiceItem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoiceId'], 'required'],
            [['invoiceId'], 'default', 'value' => null],
            [['invoiceId'], 'integer'],
            [['totalIn', 'totalOut', 'revenue', 'balance'], 'number'],
            [['type', 'notes'], 'string'],
            [['lastLogAt'], 'safe'],
            [['number'], 'string', 'max' => 16],
            [['title'], 'string', 'max' => 128],
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
            'number' => Yii::t('app', 'Number'),
            'title' => Yii::t('app', 'Title'),
            'totalIn' => Yii::t('app', 'Total In'),
            'totalOut' => Yii::t('app', 'Total Out'),
            'revenue' => Yii::t('app', 'Revenue'),
            'balance' => Yii::t('app', 'Balance'),
            'type' => Yii::t('app', 'Type'),
            'notes' => Yii::t('app', 'Notes'),
            'lastLogAt' => Yii::t('app', 'Last Log At'),
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
     * @return InvoiceItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceItemQuery(get_called_class());
    }
}
