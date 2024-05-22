<?php


namespace app\modules\app\scripts\password;


use app\components\Script;
use app\models\ext\User;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use Yii;
use yii\base\Exception;

/**
 * Class ResetPasswordScript
 * @package app\modules\app\scripts\users
 */
class ResetPasswordScript extends Script
{
    /**
     *
     */
    const DEFAULT_PASSWORD_LENGTH = 8;

    /**
     * @var int|null
     */
    public ?int $userId = null;

    /**
     * @var string|null
     */
    public ?string $password = null;

    /**
     * @var User|null
     */
    private ?User $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['userId', ExistValidator::class, 'targetClass' => User::class, 'targetAttribute' => 'id'],
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
            'user' => $this->_entity->toArray(),
            'password' => $this->password,
        ];
    }

    /**
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->password = $this->password ?: Yii::$app->security->generateRandomString(self::DEFAULT_PASSWORD_LENGTH);

        $this->_entity = User::findOne($this->userId) ?: User::loggedIn();
        $this->_entity->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->_entity->accessToken = Yii::$app->security->generateRandomString();

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}