<?php


namespace app\modules\app\scripts\reference;


use app\models\ext\Company;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class GetItemScript
 * @package app\modules\app\scripts\reference
 */
class GetItemScript extends Script
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
     * @var Company|null
     */
    private ?ActiveRecord $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            [['id', 'targetClass'], 'required'],
            ['id', ExistValidator::class, 'targetClass' => Company::class]
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
     *
     * @throws InvalidConfigException
     */
    protected function onExecute(): void
    {
        $this->_entity = call_user_func([Yii::createObject($this->targetClass), 'findOne'], $this->id);
    }
}