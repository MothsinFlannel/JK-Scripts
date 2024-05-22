<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property int $locationId
 * @property string $name
 *
 * @property Location $location
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId'], 'default', 'value' => null],
            [['locationId'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32],
            [['name', 'locationId'], 'unique', 'targetAttribute' => ['name', 'locationId']],
            [['locationId'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['locationId' => 'id']],
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
            'name' => Yii::t('app', 'Name'),
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
     * {@inheritdoc}
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
}
