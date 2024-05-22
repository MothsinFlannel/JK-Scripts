<?php


namespace app\modules\app\controllers;


use app\models\ext\Company;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\companies\CompaniesListScript;
use app\modules\app\scripts\companies\DeleteCompanyScript;
use app\modules\app\scripts\companies\EditCompanyScript;
use app\modules\app\scripts\companies\GetCompanyScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class CompaniesController
 * @package app\modules\app\controllers
 */
class CompaniesController extends Controller
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
                'filters' => [
                    'query' => null
                ],
                'sort' => 'name+asc',
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new CompaniesListScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $company = Company::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$company->id,
            ];
        });

        return (new GetCompanyScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'company' => [
                    'name' => $faker->company,
                    'contactName' => $faker->name,
                    'contactEmail' => $faker->safeEmail,
                    'invoicingOnline' => true,
                    'isActive' => true,
                ]
            ];
        });

        return (new EditCompanyScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $company = Company::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$company->id,
                'company' => @$company->toArray([
                    'name',
                    'contactName',
                    'contactEmail',
                    'invoicingOnline',
                    'isActive'
                ])
            ];
        });

        return (new EditCompanyScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $company = Company::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$company->id,
            ];
        });

        return (new DeleteCompanyScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}