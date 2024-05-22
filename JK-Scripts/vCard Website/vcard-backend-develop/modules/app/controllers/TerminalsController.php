<?php


namespace app\modules\app\controllers;


use app\models\Audit;
use app\models\CabinetType;
use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\terminals\DeleteTerminalScript;
use app\modules\app\scripts\terminals\EditTerminalScript;
use app\modules\app\scripts\terminals\ExportTerminalsScript;
use app\modules\app\scripts\terminals\GetTerminalScript;
use app\modules\app\scripts\terminals\TerminalMovementsScript;
use app\modules\app\scripts\terminals\TerminalsListScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\Inflector;
use vr\core\PagedListScript;
use Yii;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class TerminalsController
 * @package app\modules\app\controllers
 */
class TerminalsController extends Controller
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
            $terminal = Terminal::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'number' => null,
                    'licenseNumber' => null,
                    'programName' => null,
                    'active' => null,
                    'locationId' => $terminal->locationId,
                    'machineTypeId' => null,
                    'cabinetTypeId' => null,
                    'companyId' => null,
                    'warehouseId' => $terminal->warehouseId,
                    'offline' => null,
                    'cabinetAssetNumber' => null,
                    'boardAssetNumber' => null,
                    'referenceNumber' => null,
                    'ids' => null,
                    'includeTestOnes' => false,
                ],
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'sort' => null,
            ];
        });

        return (new TerminalsListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionExport(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $terminal = Terminal::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,
                    'activeOnly' => false,
                    'locationId' => $terminal->locationId,
                    'offline' => null,
                    'ids' => null,
                ],
                'sort' => null,
            ];
        });

        return (new ExportTerminalsScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $terminal = Terminal::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$terminal->id,
            ];
        });

        return (new GetTerminalScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $faker = Factory::create();

            $location = Location::find()->random()->one();
            $cabinetType = CabinetType::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'terminal' => [
                    'locationId' => $location->id,
                    'cabinetTypeId' => $cabinetType->id,
                    'number' => $faker->numberBetween(1, 255),
                    'programName' => Inflector::titleize($faker->sentence),
                    'splitPercent' => $location->splitPercent,
                    'flatFee' => $location->flatFee,
                    'groupName' => null,
                    'placementDate' => null,
                    'refillDate' => null,
                    'padlockId' => null,
                    'doorLockId' => null,
                    'notes' => null,
                    'referenceNumber' => null,
                ]
            ];
        });

        return (new EditTerminalScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $terminal = Terminal::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$terminal->id,
                'terminal' => @$terminal->toArray([
                    'locationId',
                    'cabinetTypeId',
                    'number',
                    'programName',
                    'splitPercent',
                    'flatFee',
                    'groupName',
                    'placementDate',
                    'refillDate',
                    'padlockId',
                    'doorLockId',
                    'notes',
                    'referenceNumber'
                ])
            ];
        });

        return (new EditTerminalScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $terminal = Terminal::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$terminal->id,
            ];
        });

        return (new DeleteTerminalScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionMovements(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            $audit = Audit::find()->andWhere([
                'entity' => Terminal::tableName(),
                'attribute' => ['locationId', 'warehouseId'],
            ])->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$audit->identifier,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
            ];
        });

        return (new TerminalMovementsScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}