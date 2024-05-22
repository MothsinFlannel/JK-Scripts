<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "game".
 *
 * @property int $id
 * @property string $name
 */
class Game extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'game';
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
     * {@inheritdoc}
     * @return GameQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GameQuery(get_called_class());
    }
}
