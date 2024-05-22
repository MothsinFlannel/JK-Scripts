<?php

namespace app\modules\app\controllers;

use app\models\ext\Location;
use app\modules\app\components\Controller;
use Throwable;
use vr\api\components\VerboseException;
use Yii;
use yii\base\InvalidConfigException;

/**
 *
 */
class SendGridController extends Controller
{
    /**
     * @var string[]
     */
    public $authExcept = ['*'];

    /**
     * @return void
     * @throws VerboseException
     * @throws InvalidConfigException
     */
    public function actionTest(): void
    {
        $this->checkInputParams(function () {

        });

        Yii::$app->get('sendgrid')->serviceRequest(new Location([
            'name' => 'PA4.01-MEDIA GROCERY',
        ]), 'Game #4 is not powering on and needs to be replaced.');
    }

    /**
     * @return void
     * @throws VerboseException
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function actionOffline(): void
    {
        $this->checkInputParams(function () {

        });

        Yii::$app->get('sendgrid')->offlineLocations(Location::find()->offline()->orderBy('name asc')->all());
    }
}