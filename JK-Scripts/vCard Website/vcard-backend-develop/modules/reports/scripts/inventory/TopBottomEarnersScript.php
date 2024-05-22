<?php


namespace app\modules\reports\scripts\inventory;


use app\components\ExportQueryTrait;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\MachineType;
use app\modules\app\components\FilterByTrait;
use DateTime;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class TopBottomEarnersScript
 * @package app\modules\reports\scripts\inventory
 */
class TopBottomEarnersScript extends PagedListScript
{
    use FilterByTrait;
    use ExportQueryTrait;

    /**
     * @var string|null
     */
    public ?string $since = null;

    /**
     * @var string|null
     */
    public ?string $until = null;

    /**
     * @var bool
     */
    public bool $export = false;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var ActiveQuery
     */
    private ActiveQuery $_query;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['since', 'until'], 'required'],
            // [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['machineTypeId', ExistValidator::class, 'targetClass' => MachineType::class, 'targetAttribute' => 'id'],
                ],
                'objectify' => true
            ],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws RangeNotSatisfiableHttpException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $results = ArrayHelper::typecast($this->_query->all(), [
            'net' => 'float, %0.2f',
            'netPerDay' => 'float, %0.2f',
            'grossIncome' => 'float, %0.2f',
        ]);

        if ($this->export) {
            $headers = [
                'programName' => 'Game Title',
                'number' => 'Terminal #',
                'name' => 'Location',
                'city',
                'state',
                'zipCode',
                'county',
                'from',
                'to',
                'grossIncome',
                'net',
                'netPerDay',
                'days',
            ];
            $this->arrayToCsv($results, $headers, true);
        }

        return [
            'count' => (int)$this->_query->count(),
            'results' => $results,
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $locationQuery = Location::find()
            ->filter(@$this->filters->query)
            ->andFilterWhere([
                'location.companyId' => @$this->filters->companyId,
                'location.id' => @$this->filters->locationId,
                'location.isLive' => true,
            ])
            ->andFilterCompare('location.zipCode', @$this->filters->zipCode, 'ilike')
            ->andFilterCompare('location.state', @$this->filters->state, 'ilike')
            ->andFilterCompare('location.county', @$this->filters->county, 'ilike')
            ->andFilterCompare('location.city', @$this->filters->city, 'ilike');

        if (@$this->filters->active !== null) {
            $locationQuery->active($this->filters->active);
        }

        $days = (new DateTime($this->since))->diff(new DateTime($this->until))->days + 1;
        $this->_query = Terminal::find()
            ->select([
                'terminal.programName',
                'terminal.number',
                'location.name',
                'location.city',
                'location.state',
                'location.zipCode',
                'location.county',
                'from' => new Expression(':from::date', [':from' => $this->since]),
                'to' => new Expression(':to::date', [':to' => $this->until]),
                'grossIncome',
                'net',
                'netPerDay',
                'days' => new Expression(':days::int', [':days' => $days]),
                'location.isActive',
            ])
            ->rightJoin([
                'location' => $locationQuery
            ], '[[terminal.locationId]] = location.id')
            ->leftJoin([
                'convolution' => Convolution::find()
                    ->select([
                        'locationId',
                        'terminal',
                        'grossIncome' => 'sum([[moneyIn]])',
                        'net' => 'sum([[moneyIn]] - [[moneyOut]])',
                        'netPerDay' => new Expression('sum([[moneyIn]] - [[moneyOut]]) / :days::int', [':days' => $days])
                    ])
                    ->since($this->since)
                    ->until($this->until)
                    ->groupBy(['locationId', 'terminal'])
            ], 'location.id = [[convolution.locationId]] and terminal.number = convolution.terminal')
            ->andFilterWhere([
                'number' => @$this->filters->number,
                'machineTypeId' => @$this->filters->machineTypeId,
            ])
            ->andFilterCompare('programName', @$this->filters->programName, 'ilike')
            ->andWhere('net <> 0')
            ->asArray();

        $this->applySortingToQuery($this->_query, 'net+desc');
    }
}