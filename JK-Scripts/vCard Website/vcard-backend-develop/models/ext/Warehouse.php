<?php


namespace app\models\ext;


/**
 * Class Warehouse
 * @package app\models\ext
 */
class Warehouse extends \app\models\Warehouse
{
    /**
     * @return string[]
     */
    public function extraFields(): array
    {
        return [
            'terminalsCount'
        ];
    }

    /**
     * @return int
     */
    public function getTerminalsCount(): int
    {
        return $this->getTerminals()->count() ?: 0;
    }
}