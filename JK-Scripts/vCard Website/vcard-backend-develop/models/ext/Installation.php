<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;

/**
 * Class Installation
 * @package app\models\ext
 */
class Installation extends \app\models\Installation
{
    /**
     *
     */
    const CACHE_DURATION = 5;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class'      => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }
}