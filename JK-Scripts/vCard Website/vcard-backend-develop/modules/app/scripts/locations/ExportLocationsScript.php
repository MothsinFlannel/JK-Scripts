<?php


namespace app\modules\app\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\ext\Location;
use Throwable;
use vr\core\ArrayHelper;
use yii\db\Expression;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class ExportLocationsScript
 * @package app\modules\app\scripts\locations
 */
class ExportLocationsScript extends LocationsListScript
{
    use ExportQueryTrait;

    const HEADERS = [
        // Online
        0 => [
            'name',
            'address',
            'city',
            'state',
            'zipCode',
            'contactPhone' => 'Phone',
            'operatorEmail' => 'Email',
            'licenseNumber' => 'License #',
        ],

        // Offline
        1 => [
            'name',
            'address',
            'city',
            'state',
            'zipCode',
            'lastActivity',
            'operatorEmail' => 'Email',
            'licenseNumber' => 'License #',
        ]
    ];

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws RangeNotSatisfiableHttpException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $this->_query->offset(0)->limit(-1);

        $results = ArrayHelper::getColumn($this->_query->all(), function (Location $location) {
            return [
                'name' => $location->name,
                'address' => $location->address,
                'city' => $location->city,
                'state' => $location->state,
                'zipCode' => $location->zipCode,
                'contactPhone' => $location->contactPhone,
                'operatorEmail' => $location->getOperatorEmail(),
                'licenseNumber' => $location->licenseNumber,
                'lastActivity' => $location->lastActivityAt,
            ];
        });

        $headers = self::HEADERS[(int)@$this->filters->offline];
        $results = $this->filter($results, $headers);

        $this->arrayToCsv($results, $headers);

        return [];
    }

    /**
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        parent::onExecute();

        $this->_query->addSelect([
            'state' => new Expression('upper(state)'),
        ]);
    }
}