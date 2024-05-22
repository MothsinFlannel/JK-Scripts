<?php


namespace app\modules\api\controllers;


use app\modules\api\components\Controller;
use app\modules\api\scripts\location\OptionsScript;
use app\modules\api\scripts\location\RedemptionScript;
use app\modules\api\scripts\location\SaleScript;
use app\modules\api\scripts\location\TerminalScript;
use Throwable;
use vr\core\utils\HttpCode;
use Yii;
use yii\filters\AccessControl;
use yii\web\HttpException;

/**
 * Class LocationController
 * @package app\modules\api\controllers
 */
class LocationController extends Controller
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    // allow authenticated users only
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
                'denyCallback' => function () {
                    throw new HttpException(HttpCode::LOGIN_TIME_OUT, 'You are not logged in or login expired');
                }
            ],
        ]);
    }

    /**
     *
     * @return array
     * @throws Throwable
     */
    public function actionOptions(): array
    {
        return (new OptionsScript(Yii::$app->request->queryParams))->execute()->toArray();
    }

    /**
     *
     * @return array
     * @throws Throwable
     */
    public function actionTerminal(): array
    {
        return (new TerminalScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     *
     * @return array
     * @throws Throwable
     */
    public function actionSale(): array
    {
        return (new SaleScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     *
     * @return array
     * @throws Throwable
     */
    public function actionRedemption(): array
    {
        return (new RedemptionScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}