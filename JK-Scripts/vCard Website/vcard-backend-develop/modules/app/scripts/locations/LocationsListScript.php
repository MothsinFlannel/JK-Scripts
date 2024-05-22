<?php


namespace app\modules\app\scripts\locations;


use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\ext\User;
use app\models\LocationQuery;
use app\models\Software;
use app\models\Staff;
use app\modules\app\components\FilterByTrait;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;
use yii\db\Expression;

/**
 * Class LocationsListScript
 * @package app\modules\app\scripts\locations
 */
class LocationsListScript extends PagedListScript
{
    use FilterByTrait;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var LocationQuery
     */
    protected LocationQuery $_query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    [['active', 'offline'], 'boolean'],
                ],
                'objectify' => true
            ]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array|string[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $locations = $this->_query->all();

        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($locations, function (Location $location) {
                return $location->toArray([], ['operatorEmail']);
            }),
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_query = Location::find()
            ->select([
                'location.*',
                'operatorEmail' => 'staff.email'
            ])
            ->andFilterWhere([
                'companyId' => @$this->filters->companyId,
                'location.state' => @$this->filters->state,
                'id' => @$this->filters->ids,
                'location.isLive' => @$this->filters->live,
                'location.isActive' => @$this->filters->active,
                'location.onHold' => @$this->filters->onHold,
            ])
            ->leftJoin([
                'staff' => Staff::find()
                    ->select([
                        'email' => new Expression('string_agg(email, \',\')'),
                        '[[staff.locationId]]'
                    ])
                    ->rightJoin([
                        'user' => User::find()
                            ->select([
                                'id',
                                'email'
                            ])
                    ], '[[user.id]] = [[staff.userId]]')
                    ->groupBy('[[staff.locationId]]')
            ], '[[location.id]] = [[staff.locationId]]')
            ->filter(@$this->filters->query)
            ->offline(@$this->filters->offline)
            ->offset($this->offset)->limit($this->limit);

        $this->filterBy($this->_query, 'staff.email', @$this->filters->operatorEmail);
        $this->filterBy($this->_query, 'name', @$this->filters->name);
        $this->filterBy($this->_query, 'address', @$this->filters->address);
        $this->filterBy($this->_query, 'city', @$this->filters->city);
        $this->filterBy($this->_query, 'county', @$this->filters->county);
        $this->filterBy($this->_query, 'zipCode', @$this->filters->zipCode);
        $this->filterBy($this->_query, 'gpsNumber', @$this->filters->gpsNumber);

        if (@$this->filters->programName) {
            $this->_query->rightJoin([
                'terminal' => Terminal::find()
                    ->select('locationId')
                    ->andWhere([
                        'programName' => @$this->filters->programName
                    ])
                    ->groupBy('locationId')
            ], '[[terminal.locationId]] = [[location.id]]');
        }

        if (@$this->filters->software) {
            $this->_query->rightJoin([
                'software' => Software::find()
                    ->select('locationId')
                    ->andWhere([
                        'name' => @$this->filters->software
                    ])
                    ->groupBy('locationId')
            ], '[[software.locationId]] = [[location.id]]');
        }

        $this->applySortingToQuery($this->_query, 'name+asc');
    }
}