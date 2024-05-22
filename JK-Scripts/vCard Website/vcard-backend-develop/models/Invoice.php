<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $locationId
 * @property double $amount
 * @property string $since
 * @property string $until
 * @property string $status
 * @property string $token
 * @property string $createdAt
 * @property string $oid
 * @property double $splitPercent
 * @property int $statementId
 * @property string $notes
 *
 * @property Location $location
 * @property Statement $statement
 * @property InvoiceItem[] $invoiceItems
 * @property Payment[] $payments
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'amount', 'since', 'until'], 'required'],
            [['locationId', 'statementId'], 'default', 'value' => null],
            [['locationId', 'statementId'], 'integer'],
            [['amount', 'splitPercent'], 'number'],
            [['since', 'until', 'createdAt'], 'safe'],
            [['status', 'notes'], 'string'],
            [['token'], 'string', 'max' => 64],
            [['oid'], 'string', 'max' => 24],
            [['token'], 'unique'],
            [['locationId'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['locationId' => 'id']],
            [['statementId'], 'exist', 'skipOnError' => true, 'targetClass' => Statement::className(), 'targetAttribute' => ['statementId' => 'id']],
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
            'amount' => Yii::t('app', 'Amount'),
            'since' => Yii::t('app', 'Since'),
            'until' => Yii::t('app', 'Until'),
            'status' => Yii::t('app', 'Status'),
            'token' => Yii::t('app', 'Token'),
            'createdAt' => Yii::t('app', 'Created At'),
            'oid' => Yii::t('app', 'Oid'),
            'splitPercent' => Yii::t('app', 'Split Percent'),
            'statementId' => Yii::t('app', 'Statement ID'),
            'notes' => Yii::t('app', 'Notes'),
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
    public function getStatement()
    {
        return $this->hasOne(Statement::className(), ['id' => 'statementId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceItems()
    {
        return $this->hasMany(InvoiceItem::className(), ['invoiceId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['invoiceId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return InvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceQuery(get_called_class());
    }
}
