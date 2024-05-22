<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "machineType".
 *
 * @property int $id
 * @property string $name
 *
 * @property Terminal[] $terminals
 */
class MachineType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'machineType';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerminals()
    {
        return $this->hasMany(Terminal::className(), ['machineTypeId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return MachineTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MachineTypeQuery(get_called_class());
    }
}
