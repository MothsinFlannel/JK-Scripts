<?php


namespace app\modules\app\scripts\accesses;


use app\components\Script;
use app\models\Access;
use Throwable;
use yii\db\StaleObjectException;

/**
 * Class RevokeAccessScript
 * @package app\modules\app\scripts\accesses
 */
class RevokeAccessScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            ['id', 'required']
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Access::findOne($this->id)->delete();
    }
}