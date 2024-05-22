<?php


namespace app\models\ext;


/**
 * Class Route
 * @package app\models\ext
 */
class Route extends \app\models\Route
{
    /**
     * @return array|string[]
     */
    public function extraFields(): array
    {
        return [
            'locations',
            'locationsCount' => function () {
                return $this->getLocations()->count();
            }
        ];
    }
}