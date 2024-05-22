<?php


namespace app\modules\app\scripts\warehouses;


use app\components\Script;
use app\models\ext\Warehouse;
use app\models\WarehouseQuery;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 * Class EditWarehouseScript
 * @package app\modules\app\warehouses
 */
class EditWarehouseScript extends Script
{
    /**
     * @var array
     */
    public array $warehouse = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

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
            ['warehouse', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Warehouse::class],
            [
                'warehouse',
                NestedValidator::class,
                'rules' => [
                    [
                        'number',
                        'unique',
                        'targetClass' => Warehouse::class,
                        'targetAttribute' => ['locationId', 'number' => 'number'],
                        'filter' => function (WarehouseQuery $query) {
                            return $query->andFilterCompare('id', $this->id, '<>');
                        },
                        'message' => 'The warehouse number must be unique',
                    ]
                ],
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
            'warehouse' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Warehouse::findOne($this->id) ?: new Warehouse();
        $this->_entity->attributes = $this->warehouse;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}