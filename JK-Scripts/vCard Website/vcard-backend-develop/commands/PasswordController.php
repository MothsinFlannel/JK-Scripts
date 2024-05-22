<?php

namespace app\commands;

use app\modules\app\validators\PasswordStrengthValidator;
use yii\base\DynamicModel;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class PasswordController
 * @package app\commands
 */
class PasswordController extends Controller
{
    public function actionReset()
    {

    }

    public function actionCheck(string $password)
    {
        $model = DynamicModel::validateData([
            'password' => $password
        ], [
            ['password', PasswordStrengthValidator::class]
        ]);

        if ($model->hasErrors()) {
            Console::output(var_export($model->errors, true));
        }
    }
}