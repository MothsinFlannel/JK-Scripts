<?php


namespace app\modules\app\controllers;


use app\models\ext\Location;
use app\models\ext\Route;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\locations\DeleteLocationScript;
use app\modules\app\scripts\locations\EditLocationScript;
use app\modules\app\scripts\locations\ExportLocationsScript;
use app\modules\app\scripts\locations\GetLocationScript;
use app\modules\app\scripts\locations\LocationRevenueScript;
use app\modules\app\scripts\locations\LocationsListScript;
use app\modules\app\scripts\locations\ServiceRequestLocationScript;
use DateInterval;
use DateTime;
use Exception;
use Faker\Factory;
use Throwable;
use vr\api\components\filters\MetaSupportFilter;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class LocationsController
 * @package app\modules\app\controllers
 */
class LocationsController extends Controller
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'meta' => [
                'class' => MetaSupportFilter::class,
                'except' => ['revenue']
            ]
        ]);
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionList(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->admin()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'companyId' => null,
                    'offline' => null,
                    'active' => null,
                    'live' => null,

                    'name' => null,
                    'address' => null,
                    'city' => null,
                    'county' => null,
                    'state' => null,
                    'zipCode' => null,
                    'operatorEmail' => null,
                    'programName' => null,
                    'software' => null,
                    'gpsNumber' => null,
                    'ids' => null,
                    'onHold' => null,
                ],
                'sort' => null,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new LocationsListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionExport(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->admin()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'companyId' => null,
                    'offline' => null,
                    'active' => null,

                    'name' => null,
                    'address' => null,
                    'city' => null,
                    'state' => null,
                    'zipCode' => null,
                    'operatorEmail' => null,
                    'programName' => null,
                    'software' => null,
                    'ids' => null,
                    'county' => null,
                    'onHold' => null,
                ],
                'sort' => null,
            ];
        });

        return (new ExportLocationsScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$location->id,
            ];
        });

        return (new GetLocationScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->admin()->active()->random()->one();

            $faker = Factory::create();
            $number = Location::find()->count() + 1;

            $route = Route::find()->random()->one();

            /** @noinspection PhpUndefinedFieldInspection */
            return [
                'accessToken' => @$user->accessToken,
                'location' => [
                    'routeId' => @$route->id,
                    'name' => 'Location #' . $number,
                    'contactPhone' => $faker->phoneNumber,
                    'contactName' => $faker->name,
                    'timezone' => $faker->timezone,
                    'address' => $faker->streetAddress,
                    'county' => null,
                    'city' => $faker->city,
                    'state' => $faker->state,
                    'zipCode' => $faker->postcode,
                    'splitPercent' => $faker->randomNumber(1) * 10,
                    'flatFee' => $faker->randomNumber(1) * 10,
                    'isActive' => true,
                    'invoicingMode' => Location::INVOICING_MODE_AUTOMATIC,
                    'operatorEmail' => $user->email,
                    'serial' => (string)$faker->randomNumber(8),
                    'maxOfflineHours' => $faker->numberBetween(1, 3),
                    'maxAddCreditsAmount' => 0,
                    'enableAddCredits' => $faker->boolean(80),
                    'enableRedemption' => $faker->boolean(80),
                    'enableCreditsReplay' => $faker->boolean(80),
                    'disableScreenLock' => $faker->boolean(80),
                    'disablePrinting' => $faker->boolean(80),
                    'isLive' => true,
                    'licenseNumber' => null,
                    'gpsNumber' => null,
                    'installedAt' => null,
                    'software' => null,
                    'onHold' => false,
                ],
                'installationId' => null,
            ];
        });

        return (new EditLocationScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$location->id,
                'location' => @$location->toArray([
                    'routeId',
                    'name',
                    'serial',
                    'contactPhone',
                    'contactName',
                    'timezone',
                    'address',
                    'county',
                    'city',
                    'state',
                    'zipCode',
                    'splitPercent',
                    'flatFee',
                    'isActive',
                    'invoicingMode',
                    'isLive',
                    'licenseNumber',
                    'gpsNumber',
                    'installedAt',
                    'software',
                    'onHold',
                ])
            ];
        });

        return (new EditLocationScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$location->id,
            ];
        });

        return (new DeleteLocationScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Exception
     */
    public function actionRevenue(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'locationId' => $location->id,
                'since' => (new DateTime('this week'))->format('Y-m-d'),
                'until' => (new DateTime('this week'))
                    ->add(DateInterval::createFromDateString('+6 days'))->format('Y-m-d'),
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'export' => false,
                'sort' => null,
            ];
        });

        return (new LocationRevenueScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionServiceRequest(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$location->id,
                'note' => 'Hello',
            ];
        });

        return (new ServiceRequestLocationScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}