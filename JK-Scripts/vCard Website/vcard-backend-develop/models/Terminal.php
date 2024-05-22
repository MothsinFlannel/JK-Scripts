<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "terminal".
 *
 * @property int $id
 * @property int $locationId
 * @property int $cabinetTypeId
 * @property int $number
 * @property string $programName
 * @property double $splitPercent
 * @property double $flatFee
 * @property string $groupName
 * @property string $lastActivityAt
 * @property string $createdAt
 * @property string $oid
 * @property int $machineTypeId
 * @property string $licenseNumber
 * @property string $cabinetAssetNumber
 * @property string $boardAssetNumber
 * @property int $warehouseId
 * @property string $archivedAt
 * @property string $placementDate
 * @property string $refillDate
 * @property int $padlockId
 * @property int $doorLockId
 * @property string $notes
 * @property string $referenceNumber
 *
 * @property CabinetType $cabinetType
 * @property DoorLock $doorLock
 * @property Location $location
 * @property MachineType $machineType
 * @property Padlock $padlock
 * @property Warehouse $warehouse
 */
class Terminal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terminal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'cabinetTypeId', 'number', 'machineTypeId', 'warehouseId', 'padlockId', 'doorLockId'], 'default', 'value' => null],
            [['locationId', 'cabinetTypeId', 'number', 'machineTypeId', 'warehouseId', 'padlockId', 'doorLockId'], 'integer'],
            [['number'], 'required'],
            [['splitPercent', 'flatFee'], 'number'],
            [['lastActivityAt', 'createdAt', 'archivedAt', 'placementDate', 'refillDate'], 'safe'],
            [['notes'], 'string'],
            [['programName', 'groupName', 'referenceNumber'], 'string', 'max' => 64],
            [['oid'], 'string', 'max' => 24],
            [['licenseNumber', 'cabinetAssetNumber', 'boardAssetNumber'], 'string', 'max' => 32],
            [['oid'], 'unique'],
            [['cabinetTypeId'], 'exist', 'skipOnError' => true, 'targetClass' => CabinetType::className(), 'targetAttribute' => ['cabinetTypeId' => 'id']],
            [['doorLockId'], 'exist', 'skipOnError' => true, 'targetClass' => DoorLock::className(), 'targetAttribute' => ['doorLockId' => 'id']],
            [['locationId'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['locationId' => 'id']],
            [['machineTypeId'], 'exist', 'skipOnError' => true, 'targetClass' => MachineType::className(), 'targetAttribute' => ['machineTypeId' => 'id']],
            [['padlockId'], 'exist', 'skipOnError' => true, 'targetClass' => Padlock::className(), 'targetAttribute' => ['padlockId' => 'id']],
            [['warehouseId'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouseId' => 'id']],
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
            'cabinetTypeId' => Yii::t('app', 'Cabinet Type ID'),
            'number' => Yii::t('app', 'Number'),
            'programName' => Yii::t('app', 'Program Name'),
            'splitPercent' => Yii::t('app', 'Split Percent'),
            'flatFee' => Yii::t('app', 'Flat Fee'),
            'groupName' => Yii::t('app', 'Group Name'),
            'lastActivityAt' => Yii::t('app', 'Last Activity At'),
            'createdAt' => Yii::t('app', 'Created At'),
            'oid' => Yii::t('app', 'Oid'),
            'machineTypeId' => Yii::t('app', 'Machine Type ID'),
            'licenseNumber' => Yii::t('app', 'License Number'),
            'cabinetAssetNumber' => Yii::t('app', 'Cabinet Asset Number'),
            'boardAssetNumber' => Yii::t('app', 'Board Asset Number'),
            'warehouseId' => Yii::t('app', 'Warehouse ID'),
            'archivedAt' => Yii::t('app', 'Archived At'),
            'placementDate' => Yii::t('app', 'Placement Date'),
            'refillDate' => Yii::t('app', 'Refill Date'),
            'padlockId' => Yii::t('app', 'Padlock ID'),
            'doorLockId' => Yii::t('app', 'Door Lock ID'),
            'notes' => Yii::t('app', 'Notes'),
            'referenceNumber' => Yii::t('app', 'Reference Number'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCabinetType()
    {
        return $this->hasOne(CabinetType::className(), ['id' => 'cabinetTypeId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoorLock()
    {
        return $this->hasOne(DoorLock::className(), ['id' => 'doorLockId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'locationId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachineType()
    {
        return $this->hasOne(MachineType::className(), ['id' => 'machineTypeId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPadlock()
    {
        return $this->hasOne(Padlock::className(), ['id' => 'padlockId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouseId']);
    }

    /**
     * {@inheritdoc}
     * @return TerminalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TerminalQuery(get_called_class());
    }
}
