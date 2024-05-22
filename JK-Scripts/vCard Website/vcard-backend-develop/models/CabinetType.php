<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cabinetType".
 *
 * @property int $id
 * @property string $name
 * @property bool $isActive
 *
 * @property Terminal[] $terminals
 */
class CabinetType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cabinetType';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['isActive'], 'boolean'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'isActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerminals()
    {
        return $this->hasMany(Terminal::className(), ['cabinetTypeId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CabinetTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CabinetTypeQuery(get_called_class());
    }
}
