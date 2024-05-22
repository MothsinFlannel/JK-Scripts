<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property string $serial
 * @property int $terminal
 * @property double $moneyIn
 * @property double $moneyOut
 * @property string $createdAt
 * @property bool $isLive
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['serial', 'terminal', 'createdAt', 'isLive'], 'required'],
            [['terminal'], 'default', 'value' => null],
            [['terminal'], 'integer'],
            [['moneyIn', 'moneyOut'], 'number'],
            [['createdAt'], 'safe'],
            [['isLive'], 'boolean'],
            [['serial'], 'string', 'max' => 32],
            [['serial', 'terminal', 'createdAt', 'isLive'], 'unique', 'targetAttribute' => ['serial', 'terminal', 'createdAt', 'isLive']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'serial' => Yii::t('app', 'Serial'),
            'terminal' => Yii::t('app', 'Terminal'),
            'moneyIn' => Yii::t('app', 'Money In'),
            'moneyOut' => Yii::t('app', 'Money Out'),
            'createdAt' => Yii::t('app', 'Created At'),
            'isLive' => Yii::t('app', 'Is Live'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return LogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LogQuery(get_called_class());
    }
}
