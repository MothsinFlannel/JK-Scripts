<?php


namespace app\modules\app\controllers;


use app\models\ext\User;
use app\models\Installation;
use app\modules\app\components\Controller;
use app\modules\app\scripts\installations\DeleteInstallationScript;
use app\modules\app\scripts\installations\GetInstallationScript;
use app\modules\app\scripts\installations\InstallationListScript;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class InstallationsController
 * @package app\modules\app\controllers
 */
class InstallationsController extends Controller
{
    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionList(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new InstallationListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $email = Installation::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$email->id,
            ];
        });

        return (new GetInstallationScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $email = Installation::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$email->id,
            ];
        });

        return (new DeleteInstallationScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}