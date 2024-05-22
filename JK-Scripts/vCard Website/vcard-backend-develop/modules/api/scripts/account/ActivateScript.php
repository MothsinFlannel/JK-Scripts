<?php


namespace app\modules\api\scripts\account;


use app\components\Script;
use app\models\ext\User;
use app\models\Installation;
use vr\core\ErrorsException;
use vr\core\utils\HttpCode;
use vr\core\validators\ExistValidator;
use yii\db\Expression;

/**
 * Class ActivateScript
 * @package app\modules\api\scripts\account
 */
class ActivateScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $serial = null;

    /**
     * @var string|null
     */
    public ?string $email = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['serial', 'email'], 'required'],
            ['email', 'email'],
            [
                'email',
                ExistValidator::class,
                'targetClass' => User::class,
                'statusCode' => HttpCode::FORBIDDEN,
            ],
        ];
    }

    /**
     *
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        if (!Installation::findOne(['serial' => $this->serial])) {
            $installation = new Installation([
                'serial' => $this->serial,
                'email' => $this->email,
                'createdAt' => new Expression('now()'),
            ]);

            if (!$installation->save() || !$installation->refresh()) {
                throw new ErrorsException($installation->errors);
            }
        }
    }
}