<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property string $name
 * @property string $contactPhone
 * @property string $timezone
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipCode
 * @property double $splitPercent
 * @property double $flatFee
 * @property string $serial
 * @property int $maxOfflineHours
 * @property double $maxAddCreditsAmount
 * @property bool $enableAddCredits
 * @property bool $enableRedemption
 * @property bool $enableCreditsReplay
 * @property string $lastActivityAt
 * @property bool $isActive
 * @property string $createdAt
 * @property string $oid
 * @property int $routeId
 * @property string $invoicingPeriod
 * @property string $invoicingDays
 * @property bool $isLive
 * @property int $companyId
 * @property bool $disableScreenLock
 * @property bool $disablePrinting
 * @property string $licenseNumber
 * @property string $notes
 * @property string $gpsNumber
 * @property string $county
 * @property string $contactName
 * @property string $installedAt
 * @property string $invoicingMode
 * @property int $maxTerminalNumber
 * @property bool $onHold
 *
 * @property Category[] $categories
 * @property Clerk[] $clerks
 * @property Convolution[] $convolutions
 * @property Invoice[] $invoices
 * @property Company $company
 * @property Route $route
 * @property NotificationEmail[] $notificationEmails
 * @property Redemption[] $redemptions
 * @property Sale[] $sales
 * @property Software[] $softwares
 * @property Staff[] $staff
 * @property User[] $users
 * @property Terminal[] $terminals
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'city', 'state', 'zipCode', 'splitPercent', 'invoicingMode'], 'required'],
            [['splitPercent', 'flatFee', 'maxAddCreditsAmount'], 'number'],
            [['maxOfflineHours', 'routeId', 'companyId', 'maxTerminalNumber'], 'default', 'value' => null],
            [['maxOfflineHours', 'routeId', 'companyId', 'maxTerminalNumber'], 'integer'],
            [['enableAddCredits', 'enableRedemption', 'enableCreditsReplay', 'isActive', 'isLive', 'disableScreenLock', 'disablePrinting', 'onHold'], 'boolean'],
            [['lastActivityAt', 'createdAt', 'installedAt'], 'safe'],
            [['invoicingPeriod', 'notes', 'invoicingMode'], 'string'],
            [['name', 'city', 'invoicingDays', 'county'], 'string', 'max' => 128],
            [['contactPhone', 'timezone', 'serial', 'gpsNumber'], 'string', 'max' => 32],
            [['address', 'licenseNumber', 'contactName'], 'string', 'max' => 256],
            [['state'], 'string', 'max' => 64],
            [['zipCode'], 'string', 'max' => 16],
            [['oid'], 'string', 'max' => 24],
            [['oid'], 'unique'],
            [['serial'], 'unique'],
            [['companyId'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['companyId' => 'id']],
            [['routeId'], 'exist', 'skipOnError' => true, 'targetClass' => Route::className(), 'targetAttribute' => ['routeId' => 'id']],
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
            'contactPhone' => Yii::t('app', 'Contact Phone'),
            'timezone' => Yii::t('app', 'Timezone'),
            'address' => Yii::t('app', 'Address'),
            'city' => Yii::t('app', 'City'),
            'state' => Yii::t('app', 'State'),
            'zipCode' => Yii::t('app', 'Zip Code'),
            'splitPercent' => Yii::t('app', 'Split Percent'),
            'flatFee' => Yii::t('app', 'Flat Fee'),
            'serial' => Yii::t('app', 'Serial'),
            'maxOfflineHours' => Yii::t('app', 'Max Offline Hours'),
            'maxAddCreditsAmount' => Yii::t('app', 'Max Add Credits Amount'),
            'enableAddCredits' => Yii::t('app', 'Enable Add Credits'),
            'enableRedemption' => Yii::t('app', 'Enable Redemption'),
            'enableCreditsReplay' => Yii::t('app', 'Enable Credits Replay'),
            'lastActivityAt' => Yii::t('app', 'Last Activity At'),
            'isActive' => Yii::t('app', 'Is Active'),
            'createdAt' => Yii::t('app', 'Created At'),
            'oid' => Yii::t('app', 'Oid'),
            'routeId' => Yii::t('app', 'Route ID'),
            'invoicingPeriod' => Yii::t('app', 'Invoicing Period'),
            'invoicingDays' => Yii::t('app', 'Invoicing Days'),
            'isLive' => Yii::t('app', 'Is Live'),
            'companyId' => Yii::t('app', 'Company ID'),
            'disableScreenLock' => Yii::t('app', 'Disable Screen Lock'),
            'disablePrinting' => Yii::t('app', 'Disable Printing'),
            'licenseNumber' => Yii::t('app', 'License Number'),
            'notes' => Yii::t('app', 'Notes'),
            'gpsNumber' => Yii::t('app', 'Gps Number'),
            'county' => Yii::t('app', 'County'),
            'contactName' => Yii::t('app', 'Contact Name'),
            'installedAt' => Yii::t('app', 'Installed At'),
            'invoicingMode' => Yii::t('app', 'Invoicing Mode'),
            'maxTerminalNumber' => Yii::t('app', 'Max Terminal Number'),
            'onHold' => Yii::t('app', 'On Hold'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClerks()
    {
        return $this->hasMany(Clerk::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConvolutions()
    {
        return $this->hasMany(Convolution::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'companyId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoute()
    {
        return $this->hasOne(Route::className(), ['id' => 'routeId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationEmails()
    {
        return $this->hasMany(NotificationEmail::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRedemptions()
    {
        return $this->hasMany(Redemption::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sale::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSoftwares()
    {
        return $this->hasMany(Software::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasMany(Staff::className(), ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->viaTable('staff', ['locationId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerminals()
    {
        return $this->hasMany(Terminal::className(), ['locationId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return LocationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LocationQuery(get_called_class());
    }
}
