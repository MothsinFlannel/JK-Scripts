<?php


namespace app\modules\app\scripts\warehouses;


use app\models\ext\Warehouse;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetWarehouseScript
 * @package app\modules\app\warehouses
 */
class GetWarehouseScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var Warehouse|null
     */
    private ?Warehouse $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Warehouse::class]
        ];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'warehouse' => $this->_entity->toArray([], ['terminalsCount']),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Warehouse::findOne($this->id);
    }
}