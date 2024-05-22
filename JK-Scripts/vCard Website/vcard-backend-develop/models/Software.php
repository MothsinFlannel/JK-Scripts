<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "software".
 *
 * @property int $id
 * @property string $name
 * @property string $serverNo
 * @property int $maxMachineCount
 * @property bool $isMobileOnly
 * @property double $split
 * @property string $installDate
 * @property string $notes
 * @property int $locationId
 * @property bool $isFrozen
 *
 * @property Location $location
 */
class Software extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'software';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'locationId'], 'required'],
            [['maxMachineCount', 'locationId'], 'default', 'value' => null],
            [['maxMachineCount', 'locationId'], 'integer'],
            [['isMobileOnly', 'isFrozen'], 'boolean'],
            [['split'], 'number'],
            [['installDate'], 'safe'],
            [['notes'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['serverNo'], 'string', 'max' => 16],
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
            'name' => Yii::t('app', 'Name'),
            'serverNo' => Yii::t('app', 'Server No'),
            'maxMachineCount' => Yii::t('app', 'Max Machine Count'),
            'isMobileOnly' => Yii::t('app', 'Is Mobile Only'),
            'split' => Yii::t('app', 'Split'),
            'installDate' => Yii::t('app', 'Install Date'),
            'notes' => Yii::t('app', 'Notes'),
            'locationId' => Yii::t('app', 'Location ID'),
            'isFrozen' => Yii::t('app', 'Is Frozen'),
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
     * @return SoftwareQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SoftwareQuery(get_called_class());
    }
}
