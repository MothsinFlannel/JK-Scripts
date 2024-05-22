<?php


namespace app\modules\app\scripts\routes;


use app\models\ext\Location;
use app\models\ext\Route;
use app\models\RouteQuery;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;
use yii\db\Expression;

/**
 * Class RoutesListScript
 * @package app\modules\app\scripts\routes
 */
class RoutesListScript extends PagedListScript
{
    /**
     * @var array | object
     */
    public array|object $filters = [];
    /**
     * @var RouteQuery
     */
    private RouteQuery $_query;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['query', 'trim']
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
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Route $route) {
                return $route->toArray([], ['locationsCount']);
            })
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_query = Route::find()
            ->select([
                'route.*',
                'locationsCount' => new Expression('coalesce(location.count, 0)')
            ])
            ->leftJoin([
                'location' => Location::find()
                    ->select([
                        'routeId',
                        'count' => 'count(*)'
                    ])
                    ->groupBy('routeId')
            ], '[[location.routeId]] = [[route.id]]')
            ->filter(@$this->filters->query)
            ->accessible()
            ->offset($this->offset)->limit($this->limit);

        $this->applySortingToQuery($this->_query, 'name+asc');
    }
}