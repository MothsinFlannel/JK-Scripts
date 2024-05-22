<?php


namespace app\modules\app\controllers;


use app\models\ext\Company;
use app\modules\app\components\Controller;
use app\modules\app\scripts\statements\GetStatementScript;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use Yii;

/**
 * Class StatementsController
 * @package app\modules\app\controllers
 */
class StatementsController extends Controller
{
    public $authExcept = ['get'];

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $company = Company::find()->random()->one();
            return [
                'token' => @$company->token,
            ];
        });

        return (new GetStatementScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}