<?php


namespace app\modules\api\components;

use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Class Controller
 * @package app\modules\api\components
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @return array|array[]
     */
    public function behaviors(): array
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ]
        ];
    }
}