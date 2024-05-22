<?php


namespace app\modules\app\scripts\terminals;


use app\models\ext\Terminal;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use yii\db\Expression;

/**
 * Class GetTerminalScript
 * @package app\modules\app\terminals
 */
class GetTerminalScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var Terminal|null
     */
    private ?Terminal $_entity;

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
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'terminal' => $this->_entity->toArray([
                '*',
                'location.id',
                'location.name',
                'location.address',
                'location.city',
                'location.state',
            ], ['cabinetType', 'machineType', 'location', 'padlock', 'doorLock'])
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Terminal::find()
            ->joinWith('location')
            ->select([
                'terminal.*',
                'lastActivityAt' => new Expression("timezone(location.timezone, [[terminal.lastActivityAt]])")
            ])
            ->andWhere(['terminal.id' => $this->id])
            ->one();
    }
}