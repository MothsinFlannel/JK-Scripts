<?php

namespace app\modules\app\scripts\password;

use app\components\Script;
use app\models\ext\User;
use app\modules\app\validators\PasswordStrengthValidator;
use Exception;
use vr\core\ErrorsException;
use Yii;

/**
 *
 */
class ChangePasswordScript extends Script
{
    /**
     * @var string|null
     */
    public string|null $oldPassword = null;

    /**
     * @var string|null
     */
    public string|null $newPassword = null;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['oldPassword', 'newPassword'], 'required'],
            ['oldPassword', 'checkPassword'],
            [
                'newPassword',
                'compare',
                'compareAttribute' => 'oldPassword',
                'operator' => '!=',
                'message' => 'New password must be different from the old one',
                'when' => function () {
                    return !$this->hasErrors('oldPassword');
                }
            ],
            ['newPassword', PasswordStrengthValidator::class, 'strong' => false]
        ];
    }

    public function checkPassword(): void
    {
        if (!Yii::$app->security->validatePassword($this->oldPassword, User::loggedIn()->password)) {
            $this->addError('oldPassword', 'Old password is invalid');
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $user = User::loggedIn();
        $user->password = Yii::$app->security->generatePasswordHash($this->newPassword);

        if (!$user->save() || !$user->refresh()) {
            throw new ErrorsException($user->errors);
        }
    }
}