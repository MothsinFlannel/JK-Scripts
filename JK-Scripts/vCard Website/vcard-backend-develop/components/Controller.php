<?php


namespace app\components;

use Exception;
use vr\api\components\filters\MetaSupportFilter;
use vr\rbac\RbacAccessControl;

/**
 * Class Controller
 * @package app\components
 */
class Controller extends \vr\api\components\Controller
{
    /**
     * @return array
     * @throws Exception
     */
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'rbac' => [
                'class' => RbacAccessControl::class
            ],
            'meta' => [
                'class' => MetaSupportFilter::class,
            ]
        ]);
    }
}