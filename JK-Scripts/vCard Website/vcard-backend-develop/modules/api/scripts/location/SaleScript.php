<?php


namespace app\modules\api\scripts\location;


use app\components\Script;
use app\models\ext\Location;
use app\models\Sale;
use app\modules\api\components\LocationActivityTrait;
use app\modules\api\validators\LocationValidator;
use Throwable;
use vr\core\ErrorsException;
use yii\web\NotFoundHttpException;

/**
 * Class SaleScript
 * @package app\modules\api\scripts\location
 */
class SaleScript extends Script
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
    public float $amount;

    /**
     * @var string
     */
    public string $clerk;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['serial', 'terminal', 'amount', 'timestamp', 'clerk'], 'required'],
            ['serial', LocationValidator::class],
            [['terminal', 'amount'], 'number'],
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
        $location = Location::findOne(['serial' => $this->serial]);

        $sale = new Sale([
            'createdAt' => $this->timestamp,
        ]);

        $sale->locationId = $location->id;
        $sale->amount = $this->amount;
        $sale->terminal = $this->terminal;
        $sale->clerk = $this->clerk;

        if (!$sale->save() || !$sale->refresh()) {
            throw  new ErrorsException($sale->errors);
        }

        $this->refreshLastActivity($this->serial);
    }
}