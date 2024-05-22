<?php


namespace app\modules\app\controllers;


use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\charts\RevenueChartScript;
use DateTime;
use Exception;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use Yii;

/**
 * Class ChartsController
 * @package app\modules\app\controllers
 */
class ChartsController extends Controller
{
    /**
     * @throws VerboseException
     * @throws ErrorsException
     * @throws Exception
     */
    public function actionRevenue(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'since' => (new DateTime('-6 days'))->format('Y-m-d'),
                'until' => (new DateTime())->format('Y-m-d'),
                'filters' => [
                    'locationId' => null,
                    'companyId' => null,
                ]
            ];
        });

        return (new RevenueChartScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}