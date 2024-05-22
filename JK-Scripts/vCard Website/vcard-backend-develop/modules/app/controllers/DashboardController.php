<?php


namespace app\modules\app\controllers;

use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\dashboard\DashboardIndexScript;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;

/**
 * Class DashboardController
 * @package app\modules\app\controllers
 */
class DashboardController extends Controller
{
    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionIndex(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,
                    'companyId' => null
                ],
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new DashboardIndexScript())->execute()->toArray();
    }
}