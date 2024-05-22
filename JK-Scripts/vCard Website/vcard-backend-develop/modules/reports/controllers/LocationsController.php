<?php /** @noinspection DuplicatedCode */


namespace app\modules\reports\controllers;


use app\components\Controller;
use app\models\ext\Location;
use app\models\ext\User;
use app\models\Redemption;
use app\modules\reports\scripts\locations\AllLocationsStandardReport;
use app\modules\reports\scripts\locations\DevicePerformanceReportScript;
use app\modules\reports\scripts\locations\LocationDailyReportScript;
use app\modules\reports\scripts\locations\LocationInvoicedReportScript;
use app\modules\reports\scripts\locations\LocationStandardReportScript;
use app\modules\reports\scripts\locations\RedemptionsReportScript;
use app\modules\reports\scripts\locations\RemainsReportScript;
use app\modules\reports\scripts\locations\WeekToDateReportScript;
use DateInterval;
use DateTime;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class LocationsController
 * @package app\modules\reports\controllers
 */
class LocationsController extends Controller
{
    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionRemains(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->admin()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,
                    'companyId' => null,
                    'city' => null,
                    'state' => null,
                    'zipCode' => null,
                    'active' => null,
                ],
                'since' => (new DateTime('last week'))->format('Y-m-d'),
                'until' => (new DateTime('last week'))
                    ->add(DateInterval::createFromDateString('+6 days'))->format('Y-m-d'),
                'sort' => null,
                'export' => false
            ];
        });

        return (new RemainsReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionWeekToDate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->admin()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'offline' => null,
                    'companyId' => null,

                    'active' => null,
                    'locationId' => null,
                    'address' => null,
                    'city' => null,
                    'state' => null,
                    'zipCode' => null,
                ],
                'since' => (new DateTime('this week'))->format('Y-m-d'),
                'until' => (new DateTime('this week'))
                    ->add(DateInterval::createFromDateString('+6 days'))->format('Y-m-d'),
                'sort' => null,
                'export' => false,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new WeekToDateReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionAllLocationsStandard(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'companyId' => null,

                    'active' => null,
                    'locationId' => null,
                    'address' => null,
                    'city' => null,
                    'state' => null,
                    'zipCode' => null,
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

        return (new AllLocationsStandardReport(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionRedemptions(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $redemption = Redemption::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'locationId' => @$redemption->locationId,
                'since' => (new DateTime('-6 days'))->format('Y-m-d'),
                'until' => (new DateTime())->format('Y-m-d'),
                'export' => false,
                'sort' => null,
            ];
        });

        return (new RedemptionsReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionLocationStandard(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'locationId' => $location->id,
                'since' => (new DateTime('this week'))
                    ->setTime(0, 0)
                    ->format('Y-m-d'),
                'until' => (new DateTime('this week'))
                    ->setTime(0, 0)
                    ->add(DateInterval::createFromDateString('1 week'))
                    ->sub(DateInterval::createFromDateString('1 minute'))
                    ->format('Y-m-d'),
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'export' => false,
                'sort' => null,
            ];
        });

        return (new LocationStandardReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionLocationDaily(): array
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
                'export' => false,
                'sort' => null,
            ];
        });

        return (new LocationDailyReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionLocationInvoiced(): array
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
                'export' => false,
                'sort' => null,
            ];
        });

        return (new LocationInvoicedReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionDevicePerformance(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'companyId' => null,

                    'active' => null,
                    'locationId' => null,
                    'cabinetTypeId' => null,
                    'programName' => null,
                    'city' => null,
                    'state' => null,
                    'zipCode' => null,
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

        return (new DevicePerformanceReportScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}