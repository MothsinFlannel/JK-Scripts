<?php


namespace app\modules\app\scripts\users;


use app\components\RbacManager;
use app\components\Script;
use app\models\ext\User;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use Yii;
use yii\base\Exception;
use yii\validators\CompareValidator;

/**
 * Class EditUserScript
 * @package app\modules\app\users
 * @property User $entity
 */
class UpsertUserScript extends Script
{
    /**
     *
     */
    const DEFAULT_PASSWORD_LENGTH = 8;

    /**
     * @var array
     */
    public array $user = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var User|null
     */
    private ?User $_entity;

    /**
     * @var string|null
     */
    private ?string $_password = null;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['user', 'required'],
            ['id', ExistValidator::class, 'targetClass' => User::class],
            [
                'user',
                NestedValidator::class,
                'rules' => [
                    [
                        'isActive',
                        'compare',
                        'compareValue' => false,
                        'operator' => '!=',
                        'type' => CompareValidator::TYPE_NUMBER,
                        'when' => function () {
                            return User::loggedIn()->id == $this->id;
                        },
                        'message' => 'You cannot deactivate your own account'
                    ]
                ]
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
            'user' => $this->_entity->toArray(),
            'password' => $this->_password ?: null,
        ];
    }

    /**
     * @return User|null
     */
    public function getEntity(): ?User
    {
        return $this->_entity;
    }

    /**
     *
     * @throws ErrorsException
     * @throws Exception
     * @throws \Exception
     */
    protected function onExecute(): void
    {
        $this->_entity = User::findOne($this->id) ?: new User();
        $this->_entity->attributes = $this->user;
        $this->_entity->accessToken = $this->_entity->accessToken ?: Yii::$app->security->generateRandomString();

        if ($password = ArrayHelper::getValue($this->user, 'password')) {
            $this->_entity->password = Yii::$app->security->generatePasswordHash($password);
            if ($this->_entity->isNewRecord) {
                $this->_password = $password;
            }
        }

        if (!$this->_entity->password) {
            $this->_password = Yii::$app->security->generateRandomString(self::DEFAULT_PASSWORD_LENGTH);
            $this->_entity->password = Yii::$app->security->generatePasswordHash($this->_password);
        }

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }

        (new RbacManager())->reassign($this->_entity->id);
    }
}