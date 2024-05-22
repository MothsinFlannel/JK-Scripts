<?php


namespace app\modules\app\controllers;


use app\models\ext\Convolution;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\convolutions\ConvolutionsListScript;
use app\scripts\convolutions\GetConvolutionScript;
use DateTime;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class ConvolutionsController
 * @package app\modules\app\controllers
 */
class ConvolutionsController extends Controller
{
    /**
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionList(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $convolution = Convolution::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'since' => null,
                    'until' => null,
                ],
                'locationId' => $convolution->locationId,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new ConvolutionsListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $convolution = Convolution::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'locationId' => @$convolution->locationId,
                'terminal' => @$convolution->terminal,
                'date' => (new DateTime())->format('Y-m-d'),
            ];
        });

        return (new GetConvolutionScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}