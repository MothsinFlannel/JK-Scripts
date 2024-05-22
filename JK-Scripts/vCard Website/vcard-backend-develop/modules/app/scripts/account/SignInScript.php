<?php


namespace app\modules\app\scripts\account;


use app\models\ext\User;
use vr\core\Script;
use Yii;

/**
 * Class SignInScript
 * @package app\modules\app\scripts\account
 */
class SignInScript extends Script
{
    /**
     *
     */
    const INVALID_CREDENTIALS_MESSAGE = 'Invalid email or password';

    /**
     * @var string|null
     */
    public ?string $email = null;

    /**
     * @var string|null
     */
    public ?string $password = null;

    /**
     * @var ?User
     */
    private ?User $_entity;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'checkPassword'],
        ];
    }

    /**
     *
     */
    public function checkPassword(): void
    {
        $user = User::find()->andWhere('lower(email) = :email', [':email' => strtolower($this->email)])->one();

        if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password)) {
            $this->addError('email', self::INVALID_CREDENTIALS_MESSAGE);
        }
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $permissions = Yii::$app->authManager->getPermissionsByUser($this->_entity->id);
        return [
            'user' => $this->_entity->toArray([], ['accessToken']),
            'permissions' => array_keys($permissions),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = User::find()->andWhere('lower(email) = :email', [':email' => strtolower($this->email)])->one();

        Yii::$app->user->login($this->_entity);
    }
}