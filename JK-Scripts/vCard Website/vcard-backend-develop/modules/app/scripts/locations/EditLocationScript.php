<?php


namespace app\modules\app\scripts\locations;


use app\components\Script;
use app\models\Clerk;
use app\models\ext\Audit;
use app\models\ext\Location;
use app\models\ext\User;
use app\models\Installation;
use app\models\LocationQuery;
use app\models\Staff;
use app\scripts\convolutions\RebuildConvolutionsScript;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\db\Expression;

/**
 * Class EditLocationScript
 * @package app\modules\app\locations
 * @property Location $entity
 */
class EditLocationScript extends Script
{
    /**
     *
     */
    const DEFAULT_MAX_OFFLINE_HOURS = 12;

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var array
     */
    public array $location;

    /**
     * @var int|null
     */
    public ?int $installationId = null;

    /**
     * @var Location|null
     */
    private ?Location $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['location', 'required'],
            [
                'id',
                ExistValidator::class,
                'targetClass' => Location::class,
                'filter' => function (LocationQuery $query) {
                    !User::loggedIn()->isLive && $query->andWhere([
                        'isLive' => false,
                    ]);
                },
            ],
            ['installationId', ExistValidator::class, 'targetClass' => Installation::class, 'targetAttribute' => 'id'],
            [
                'location',
                NestedValidator::class,
                'rules' => [
                    ['operatorEmail', ExistValidator::class, 'targetClass' => User::class, 'targetAttribute' => 'email'],
                    // ['installedAt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
                ]
            ],
            ['location', 'cleanUpSerial'],
        ];
    }

    /**
     * @throws Exception
     */
    public function cleanUpSerial(): void
    {
        $serial = ArrayHelper::getValue($this->location, 'serial');
        if ($serial !== null) {
            $serial = str_pad(preg_replace('/[^0-9a-fA-F]/', '', $serial), Location::SERIAL_LENGTH, '0', STR_PAD_LEFT);
            ArrayHelper::setValue($this->location, 'serial', $serial);
        }
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'location' => $this->_entity->toArray([], ['users']),
        ];
    }

    /**
     * @return Location|null
     */
    public function getEntity(): ?Location
    {
        return $this->_entity;
    }

    /**
     * @throws ErrorsException
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_entity = Location::findOne($this->id) ?: new Location([
            'isActive' => true,
            'isLive' => false,
            'invoicingMode' => Location::INVOICING_MODE_AUTOMATIC,
            'maxOfflineHours' => self::DEFAULT_MAX_OFFLINE_HOURS
        ]);

        $isNewRecord = $this->_entity->isNewRecord;

        $this->_entity->attributes = $this->location;
        if ($this->_entity->isNewRecord) {
            $this->_entity->loadDefaultValues();
        }

        $splitPercentChanged = abs($this->_entity->getOldAttribute('splitPercent') - $this->_entity->splitPercent) > PHP_FLOAT_EPSILON;
        $serialChanged = $this->_entity->isAttributeChanged('serial');

        $this->_entity->flatFee = $this->_entity->flatFee ?: 0;
        $this->_entity->state = strtoupper($this->_entity->state);

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }

        if ($serialChanged) {
            Audit::log($this->_entity, 'serial');
        }

        if (!$isNewRecord && $splitPercentChanged) {
            $this->rebuildConvolutions();
        }

        if ($email = ArrayHelper::getValue($this->location, 'operatorEmail')) {
            $staff = $this->_entity->getStaff()->one() ?: new Staff(['locationId' => $this->_entity->id]);
            $staff->userId = User::findOne(['email' => $email])->id;
            if (!$staff->save()) {
                throw new ErrorsException($staff->errors);
            }
        }

        if (!$this->id && $this->installationId) {
            $installation = Installation::findOne($this->installationId);
            $installation->handledAt = new Expression('now()');

            if (!$installation->save() || !$installation->refresh()) {
                throw new ErrorsException($installation->errors);
            }
        }

        if ($isNewRecord) {
            $this->_entity->link('clerks', new Clerk([
                'fullName' => 'Manager (Automatic)',
                'pin' => '8888',
                'isManager' => true,
                'createdAt' => new Expression('now()'),
            ]));

            $this->_entity->link('clerks', new Clerk([
                'fullName' => 'Clerk (Automatic)',
                'pin' => '1234',
                'isManager' => false,
                'createdAt' => new Expression('now()'),
            ]));
        }
    }

    /**
     * @throws Throwable
     */
    private function rebuildConvolutions(): void
    {
        list($since, $until) = $this->_entity->getFiscalInterval();

        (new RebuildConvolutionsScript([
            'locationId' => $this->_entity->id,
            'since' => $since,
            'until' => $until,
        ]))->execute();
    }
}