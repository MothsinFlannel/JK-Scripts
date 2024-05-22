<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "padlock".
 *
 * @property int $id
 * @property string $name
 *
 * @property Terminal[] $terminals
 */
class Padlock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'padlock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 128],
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
        return $this->hasMany(Terminal::className(), ['padlockId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return PadlockQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PadlockQuery(get_called_class());
    }
}
