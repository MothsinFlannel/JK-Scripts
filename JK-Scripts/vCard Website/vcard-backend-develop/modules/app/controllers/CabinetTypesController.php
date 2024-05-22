<?php


namespace app\modules\app\controllers;

use app\models\CabinetType;
use app\models\ext\User;
use app\modules\app\scripts\cabinets\CabinetTypesListScript;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class CabinetTypesController
 * @package app\modules\app\controllers
 */
class CabinetTypesController extends ReferenceController
{
    /**
     * @var string
     */
    public string $targetClass = CabinetType::class;

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
                'filters' => [
                    'activeOnly' => false,
                ],
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'sort' => null,
            ];
        });

        return (new CabinetTypesListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}