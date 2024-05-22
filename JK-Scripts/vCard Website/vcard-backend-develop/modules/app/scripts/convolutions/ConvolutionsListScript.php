<?php


namespace app\modules\app\scripts\convolutions;


use app\models\ConvolutionQuery;
use app\models\ext\Convolution;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 * Class ConvolutionsListScript
 * @package app\modules\app\scripts\convolutions
 */
class ConvolutionsListScript extends PagedListScript
{
    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var ConvolutionQuery
     */
    private ConvolutionQuery $_query;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['locationId', 'required'],
            ['filters', NestedValidator::class, 'rules' => [], 'objectify' => true],
        ];
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
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Convolution $convolution) {
                return $convolution->toArray();
            }),
            'totals' => [
                'moneyIn' => round($this->_query->sum('[[moneyIn]]'), 2),
                'moneyOut' => round($this->_query->sum('[[moneyOut]]'), 2),
                'operatorProfit' => round($this->_query->sum('[[percentageProfit]]'), 2),
            ]
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_query = Convolution::find()
            ->since(@$this->filters->since)
            ->until(@$this->filters->until)
            ->andWhere(['locationId' => $this->locationId])
            ->orderBy('date desc')
            ->offset($this->offset)->limit($this->limit);
    }
}