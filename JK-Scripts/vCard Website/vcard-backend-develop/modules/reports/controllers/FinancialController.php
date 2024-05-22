<?php

namespace app\modules\reports\controllers;

use app\components\Controller;
use app\models\ext\User;
use app\modules\reports\scripts\financial\UninvoicedReportScript;
use DateInterval;
use DateTime;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use Yii;

/**
 *
 */
class FinancialController extends Controller
{
    /**
     * @return array[]
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionUninvoiced(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                ],

                'since' => (new DateTime('this week'))->format('Y-m-d'),
                'until' => (new DateTime('this week'))
                    ->add(DateInterval::createFromDateString('+6 days'))->format('Y-m-d'),

            ];
        });

        return (new UninvoicedReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}