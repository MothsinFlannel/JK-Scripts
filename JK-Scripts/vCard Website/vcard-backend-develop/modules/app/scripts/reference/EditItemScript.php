<?php


namespace app\modules\app\scripts\reference;


use app\models\ext\Company;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class EditItemScript
 * @package app\modules\app\scripts\reference
 */
class EditItemScript extends Script
{
    /**
     * @var string
     */
    public string $targetClass;

    /**
     * @var array
     */
    public array $item = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var Company|null
     */
    private ?ActiveRecord $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            [['targetClass', 'item'], 'required'],
            ['id', ExistValidator::class, 'targetClass' => $this->targetClass]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws InvalidConfigException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $tableName = call_user_func([Yii::createObject($this->targetClass), 'tableName']);
        return [
            $tableName => $this->_entity->toArray(),
        ];
    }

    /**
     * @throws ErrorsException
     * @throws InvalidConfigException
     */
    protected function onExecute(): void
    {
        $this->_entity = call_user_func([$this->targetClass, 'findOne'], $this->id) ?: Yii::createObject($this->targetClass);
        $this->_entity->attributes = $this->item;
        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}