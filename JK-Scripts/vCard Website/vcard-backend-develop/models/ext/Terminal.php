<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;

/**
 * Class Terminal
 * @package app\models\ext
 */
class Terminal extends \app\models\Terminal
{
    /**
     * @return array|string[]
     */
    public function extraFields(): array
    {
        return [
            'cabinetType',
            'machineType',
            'location',
            'padlock',
            'doorLock'
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class'      => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt', 'archivedAt', 'lastActivityAt']
            ]
        ];
    }
}