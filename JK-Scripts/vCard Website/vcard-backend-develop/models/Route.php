<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "route".
 *
 * @property int $id
 * @property string $name
 * @property string $createdAt
 *
 * @property Location[] $locations
 */
class Route extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['createdAt'], 'safe'],
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
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['routeId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return RouteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RouteQuery(get_called_class());
    }
}
