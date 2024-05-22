<?php


namespace app\modules\api\scripts\location;


use app\components\Script;
use app\models\Category;
use app\models\ext\Location;
use app\models\Redemption;
use app\models\RedemptionItem;
use app\modules\api\components\LocationActivityTrait;
use app\modules\api\validators\LocationValidator;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\web\NotFoundHttpException;

/**
 * Class RedemptionScript
 * @package app\modules\api\scripts\location
 */
class RedemptionScript extends Script
{
    use LocationActivityTrait;

    /**
     * @var string
     */
    public string $timestamp;

    /**
     * @var string
     */
    public string $serial;

    /**
     * @var int
     */
    public int $terminal;

    /**
     * @var float
     */
    public float $totalAmount;

    /**
     * @var float
     */
    public float $replayAmount;

    /**
     * @var int
     */
    public int $replayTerminal;

    /**
     * @var float
     */
    public float $redemptionAmount;

    /**
     * @var string
     */
    public string $clerk;

    /**
     * @var array|null
     */
    public ?array $categories;

    /**
     * @var Redemption|null
     */
    private ?Redemption $_redemption;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['serial', 'terminal', 'totalAmount', 'replayAmount', 'redemptionAmount', 'timestamp', 'clerk'], 'required'],
            ['serial', LocationValidator::class],
            //            [['terminal', 'replayTerminal'], TerminalValidator::class, 'serial' => $this->serial],
            //            ['clerk', ClerkValidator::class, 'serial' => $this->serial],
            [['terminal', 'totalAmount', 'replayAmount', 'redemptionAmount'], 'number'],
            [
                'categories',
                NestedValidator::class,
                'rules' => [
                    [['amount', 'name'], 'required'],
                    ['name', ExistValidator::class, 'targetClass' => Category::class, 'message' => 'Category not found'],
                    ['amount', 'number']
                ],
                'allowArray' => true
            ]
        ];
    }

    /**
     * @throws ErrorsException
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_redemption = new Redemption([
            'createdAt' => $this->timestamp,
        ]);

        $location = Location::findOne(['serial' => $this->serial]);

        $this->_redemption->locationId = $location->id;
        $this->_redemption->totalAmount = $this->totalAmount;
        $this->_redemption->redemptionAmount = $this->redemptionAmount;
        $this->_redemption->replayAmount = $this->replayAmount;
        $this->_redemption->terminal = $this->terminal;
        $this->_redemption->replayTerminal = $this->replayTerminal;
        $this->_redemption->clerk = $this->clerk;

        if (!$this->_redemption->save() || !$this->_redemption->refresh()) {
            throw  new ErrorsException($this->_redemption->errors);
        }

        if (is_array($this->categories)) {
            $this->saveRedemptionItems();
        }

        $this->refreshLastActivity($this->serial);
    }

    /**
     * @throws ErrorsException
     * @throws Exception
     */
    private function saveRedemptionItems(): void
    {
        foreach ($this->categories as $attributes) {
            $redemptionItem = new RedemptionItem([
                'redemptionId' => $this->_redemption->id,
                'category' => ArrayHelper::getValue($attributes, 'name'),
            ]);

            $redemptionItem->attributes = $attributes;

            if (!$redemptionItem->save()) {
                throw new ErrorsException($redemptionItem->errors);
            }
        }
    }
}