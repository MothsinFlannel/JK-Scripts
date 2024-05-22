<?php

namespace app\commands;

use app\models\ext\Location;
use app\modules\reports\scripts\locations\LocationDailyReportScript;
use Datetime;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Inflector;
use yii\web\RangeNotSatisfiableHttpException;

/**
 *
 */
class ReportController extends Controller
{
    /**
     * @return void
     * @throws Throwable
     * @throws InvalidConfigException
     */
    public function actionOfflineLocations(): void
    {
        $query = Location::find()->offline()->orderBy('name asc');
        Yii::$app->get('sendgrid')->offlineLocations($query->all());
    }

    /**
     * @param $locationId
     * @param $date
     * @return void
     * @throws ErrorsException
     * @throws RangeNotSatisfiableHttpException
     * @throws Exception
     */
    public function actionLocationDaily($locationIds, $date): void
    {
        $date = (new Datetime($date))->format('Y-m-d');

        // Temporary method. Don't pay your attention to the hardcoded constants in the code
        ini_set('error_reporting', 'E_ALL & ~E_DEPRECATED & ~E_STRICT');
        
        $message = @Yii::$app->mailer
            ->compose('location-daily', [
                'date' => $date,
            ])
            ->setTo(ArrayHelper::getValue(Yii::$app->params, ['mailer', 'defaultTo']))
            ->setFrom(ArrayHelper::getValue(Yii::$app->params, ['mailer', 'defaultFrom']))
            ->setBcc(ArrayHelper::getValue(Yii::$app->params, ['mailer', 'defaultBcc']))
            ->setSubject("Location Daily Report, $date");

        foreach (explode(',', $locationIds) as $locationId) {
            $location = Location::findOne($locationId);
            $slug = Inflector::slug($location->name);

            $output = (new LocationDailyReportScript([
                'locationId' => $locationId,
                'since' => $date,
                'until' => $date,
                'export' => true,
            ]))->execute()->export();

            $message
                ->attachContent($output, [
                    'fileName' => "location-daily-$slug-$date.csv",
                    'contentType' => 'text/csv'
                ]);
        }

        $message->send();
    }
}