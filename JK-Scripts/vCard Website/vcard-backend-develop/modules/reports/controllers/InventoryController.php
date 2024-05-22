<?php


namespace app\modules\reports\controllers;


use app\components\Controller;
use app\models\ext\User;
use app\modules\reports\scripts\inventory\InventoryCountScript;
use app\modules\reports\scripts\inventory\InventoryDetailsScript;
use app\modules\reports\scripts\inventory\PosAuditScript;
use app\modules\reports\scripts\inventory\ReconcileByDateScript;
use app\modules\reports\scripts\inventory\TopBottomEarnersScript;
use DateInterval;
use DateTime;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class InventoryController
 * @package app\modules\reports\controllers
 */
class InventoryController extends Controller
{
    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionCount(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'machineTypeId' => null,
                    'companyId' => null,

                    'active' => null,
                    'address' => null,
                    'city' => null,
                    'state' => null,
                    'zipCode' => null,
                    'county' => null,
                ],
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'export' => false,
                'sort' => null,
            ];
        });

        return (new InventoryCountScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws RangeNotSatisfiableHttpException
     * @throws VerboseException
     */
    public function actionDetails(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'machineTypeId' => null,
                    'locationId' => null,
                    'companyId' => null,
                    'licenseNumber' => null,
                    'cabinetAssetNumber' => null,
                    'boardAssetNumber' => null,
                    'cabinetTypeId' => null,
                    'programName' => null,
                    'active' => null,

                    'city' => null,
                    'state' => null,
                    'zipCode' => null,
                    'county' => null,
                ],
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'export' => false,
                'sort' => null,
            ];
        });

        return (new InventoryDetailsScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws RangeNotSatisfiableHttpException
     * @throws VerboseException
     */
    public function actionEarners(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,
                    'companyId' => null,
                    'programName' => null,
                    'number' => null,
                    'locationId' => null,
                    'machineTypeId' => null,
                    'city' => null,
                    'state' => null,
                    'county' => null,
                    'zipCode' => null,
                    'active' => null,
                ],

                'since' => (new DateTime('this week'))->format('Y-m-d'),
                'until' => (new DateTime('this week'))
                    ->add(DateInterval::createFromDateString('+6 days'))->format('Y-m-d'),

                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'export' => false,
                'sort' => null,
            ];
        });

        return (new TopBottomEarnersScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionReconcile(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'locationId' => null,
                    'companyId' => null,
                ],
                'since' => (new DateTime('first day of this month'))->format('Y-m-d'),
                'until' => (new DateTime('last day of this month'))->format('Y-m-d'),
                'export' => false,
            ];
        });

        return (new ReconcileByDateScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionPosAudit(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->admin()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,
                ],
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
            ];
        });

        return (new PosAuditScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}