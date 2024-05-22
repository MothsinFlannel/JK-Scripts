<?php

namespace app\modules\app\controllers;

use app\models\ext\Location;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\audit\AuditByAttributeScript;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use Yii;

/**
 *
 */
class AuditController extends Controller
{
    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionByAttribute(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'attribute' => 'serial',
                'value' => $location->serial,
            ];
        });

        return (new AuditByAttributeScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}