<?php


namespace app\modules\app\controllers;


use app\models\ext\Payment;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\payments\DeletePaymentScript;
use app\modules\app\scripts\payments\EditPaymentScript;
use app\modules\app\scripts\payments\GetPaymentScript;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use Yii;

/**
 * Class PaymentsController
 * @package app\modules\app\controllers
 */
class PaymentsController extends Controller
{
    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $payment = Payment::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$payment->id
            ];
        });

        return (new GetPaymentScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $payment = Payment::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$payment->id,
                'payment' => $payment->toArray(),
            ];
        });

        return (new EditPaymentScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $payment = Payment::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$payment->id
            ];
        });

        return (new DeletePaymentScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}