<?php


namespace app\modules\app\scripts\terminals;


use app\components\Script;
use app\models\ext\Terminal;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteTerminalScript
 * @package app\modules\app\terminals
 */
class DeleteTerminalScript extends Script
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
            ['id', ExistValidator::class, 'targetClass' => Terminal::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Terminal::findOne($this->id)->delete();
    }
}