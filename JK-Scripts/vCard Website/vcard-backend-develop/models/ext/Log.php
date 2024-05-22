<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;

/**
 * Class Log
 * @package app\models\ext
 */
class Log extends \app\models\Log
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }
}