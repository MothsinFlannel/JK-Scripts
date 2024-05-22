<?php

namespace app\modules\app\components;

use app\models\ext\User;
use DateTime;
use Exception;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
 *
 */
class ExpiredPasswordFilter extends ActionFilter
{
    /**
     * @param $action
     * @return bool
     * @throws Exception
     */
    public function beforeAction($action): bool
    {
        $user = User::loggedIn();

        if ($user && $user->passwordExpiresAt !== null && new DateTime($user->passwordExpiresAt) <= new DateTime()) {
            throw new ForbiddenHttpException('Your password is expired', 100);
        }

        return parent::beforeAction($action);
    }
}