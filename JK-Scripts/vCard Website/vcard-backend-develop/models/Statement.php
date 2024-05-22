<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statement".
 *
 * @property int $id
 * @property int $companyId
 * @property bool $isSent
 * @property string $createdAt
 *
 * @property Invoice[] $invoices
 */
class Statement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'statement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['companyId'], 'required'],
            [['companyId'], 'default', 'value' => null],
            [['companyId'], 'integer'],
            [['isSent'], 'boolean'],
            [['createdAt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'companyId' => Yii::t('app', 'Company ID'),
            'isSent' => Yii::t('app', 'Is Sent'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['statementId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return StatementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StatementQuery(get_called_class());
    }
}
