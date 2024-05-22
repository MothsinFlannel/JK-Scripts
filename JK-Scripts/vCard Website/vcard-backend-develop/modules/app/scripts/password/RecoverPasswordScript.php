<?php

namespace app\modules\app\scripts\password;

use app\models\ext\User;
use app\models\UserQuery;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use Yii;
use yii\base\Exception;

/**
 *
 */
class RecoverPasswordScript extends Script
{
    /**
     *
     */
    const DEFAULT_PASSWORD_LENGTH = 8;

    /**
     * @var string|null
     */
    public ?string $recoveryToken = null;

    /**
     * @var string|null
     */
    public ?string $password = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['recoveryToken'], 'required'],
            [
                'recoveryToken',
                ExistValidator::class,
                'targetClass' => User::class,
                'filter' => function (UserQuery $query) {
                    $query->andWhere([
                        'recoveryToken' => $this->recoveryToken,
                    ]);
                }
            ]
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
        return [

        ];
    }

    /**
     * @return void
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->password = $this->password ?: Yii::$app->security->generateRandomString(self::DEFAULT_PASSWORD_LENGTH);

        $user = User::findOne(['recoveryToken' => $this->recoveryToken]);
        $user->password = Yii::$app->security->generatePasswordHash($this->password);
        $user->recoveryToken = null;

        if (!$user->save(false)) {
            throw new ErrorsException($user->errors);
        }
    }
}