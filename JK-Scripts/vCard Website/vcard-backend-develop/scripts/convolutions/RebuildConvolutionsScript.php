<?php


namespace app\scripts\convolutions;


use app\components\Script;
use app\models\Convolution;
use app\models\ext\Location;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use yii\base\Event;

/**
 * Class RebuildConvolutionsScript
 * @package app\scripts\convolutions
 */
class RebuildConvolutionsScript extends Script
{
    /**
     *
     */
    const EVENT_REBUILD = 'rebuild';

    /**
     * @var string | null
     */
    public ?string $since = null;

    /**
     * @var string | null
     */
    public ?string $until = null;

    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['since', 'required'],
            //            [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
            ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @throws ErrorsException
     */
    protected function onExecute()
    {
        $query = Convolution::find()
            ->since($this->since)
            ->until($this->until)
            ->andFilterWhere(['locationId' => $this->locationId]);

        /** @var Convolution $convolution */
        foreach ($query->each() as $convolution) {
            $percentage = $convolution->location->splitPercent;

            $percentageProfit = round(($convolution->moneyIn - $convolution->moneyOut) * $percentage / 100.0, 2);
            $operatorProfit   = $convolution->percentageProfit + $convolution->flatFee;

            if ($operatorProfit !== ($percentageProfit + $convolution->location->flatFee)) {
                $this->triggerConvolutionRebuild($convolution, $percentageProfit);

                $convolution->percentageProfit = $percentageProfit;
                $convolution->flatFee          = $convolution->location->flatFee;

                if (!$convolution->save(false, ['percentageProfit', 'flatFee'])) {
                    throw new ErrorsException($convolution->errors);
                }
            }
        }
    }

    /**
     * @param Convolution $convolution
     * @param float $percentageProfit
     */
    protected function triggerConvolutionRebuild(Convolution $convolution, float $percentageProfit): void
    {
        $this->trigger(self::EVENT_REBUILD, new Event([
            'sender' => [
                'location' => $convolution->location->name,
                'terminal' => $convolution->terminal,
                'date' => $convolution->date,
                'oldAmount' => $convolution->percentageProfit + $convolution->flatFee,
                'newAmount' => $percentageProfit + $convolution->location->flatFee,
            ]
        ]));
    }
}