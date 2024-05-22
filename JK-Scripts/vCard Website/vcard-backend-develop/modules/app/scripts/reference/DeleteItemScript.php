<?php


namespace app\modules\app\scripts\reference;


use Throwable;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Class DeleteItemScript
 * @package app\modules\app\scripts\reference
 */
class DeleteItemScript extends Script
{
    /**
     * @var string
     */
    public string $targetClass;

    /**
     * @var int
     */
    public int $id;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            [['id', 'targetClass'], 'required'],
            ['id', ExistValidator::class, 'targetClass' => $this->targetClass]
        ];
    }

    /**
     * @throws ErrorsException
     * @throws InvalidConfigException
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        /** @var ActiveRecord $entity */
        $entity = call_user_func([Yii::createObject($this->targetClass), 'findOne'], $this->id);

        if (!$entity->delete()) {
            throw new ErrorsException($entity->errors);
        }
    }
}