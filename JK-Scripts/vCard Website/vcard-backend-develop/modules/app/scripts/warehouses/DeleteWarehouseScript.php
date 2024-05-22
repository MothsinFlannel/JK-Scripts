<?php


namespace app\modules\app\scripts\warehouses;


use app\components\Script;
use app\models\ext\Warehouse;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteWarehouseScript
 * @package app\modules\app\warehouses
 */
class DeleteWarehouseScript extends Script
{
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
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Warehouse::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Warehouse::findOne($this->id)->delete();
    }
}