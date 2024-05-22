<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "audit".
 *
 * @property int $id
 * @property string $entity
 * @property int $identifier
 * @property string $attribute
 * @property string $value
 * @property string $createdAt
 */
class Audit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity', 'identifier', 'attribute'], 'required'],
            [['identifier'], 'default', 'value' => null],
            [['identifier'], 'integer'],
            [['value'], 'string'],
            [['createdAt'], 'safe'],
            [['entity', 'attribute'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'entity' => Yii::t('app', 'Entity'),
            'identifier' => Yii::t('app', 'Identifier'),
            'attribute' => Yii::t('app', 'Attribute'),
            'value' => Yii::t('app', 'Value'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return AuditQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuditQuery(get_called_class());
    }
}
