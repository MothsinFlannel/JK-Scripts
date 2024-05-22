<?php

namespace app\modules\app\scripts\password;

use app\components\SendGridConnector;
use app\models\ext\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use vr\core\ErrorsException;
use vr\core\Script;
use Yii;

/**
 *
 */
class ForgotPasswordScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $email = null;

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [];
    }

    /**
     * @return void
     * @throws ErrorsException
     * @throws Exception
     * @throws GuzzleException
     */
    protected function onExecute(): void
    {
        $user = User::findOne(['email' => $this->email]);
        if ($user) {
            $user->recoveryToken = Yii::$app->security->generateRandomString(64);
            if (!$user->save(false, ['recoveryToken'])) {
                throw new ErrorsException($user->errors);
            }

            /** @var SendGridConnector $connector */
            $connector = Yii::$app->get('sendgrid');
            $connector->forgotPassword($user);
        }
    }
}