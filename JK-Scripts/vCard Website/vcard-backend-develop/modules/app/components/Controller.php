<?php


namespace app\modules\app\components;

use Exception;

/**
 * Class Controller
 * @package app\modules\app\components
 */
class Controller extends \app\components\Controller
{
    /**
     * @return array|string[]
     * @throws Exception
     */
    public function behaviors(): array
    {
        return parent::behaviors() + [
                'expiredPassword' => [
                    'class' => ExpiredPasswordFilter::class,
                ],
                'state' => [
                    'class' => StateBehavior::class,
                ]
            ];
    }
}