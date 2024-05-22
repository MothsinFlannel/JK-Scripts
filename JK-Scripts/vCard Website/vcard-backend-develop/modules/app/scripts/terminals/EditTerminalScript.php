<?php


namespace app\modules\app\scripts\terminals;


use app\components\Script;
use app\models\ext\Audit;
use app\models\ext\Terminal;
use app\models\TerminalQuery;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\db\Expression;

/**
 * Class EditTerminalScript
 * @package app\modules\app\terminals
 */
class EditTerminalScript extends Script
{
    /**
     * @var array
     */
    public array $terminal = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

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
            ['terminal', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Terminal::class],
            [
                'terminal',
                NestedValidator::class,
                'rules' => [
                    [
                        'number',
                        'unique',
                        'targetClass' => Terminal::class,
                        'targetAttribute' => 'number',
                        'filter' => function (TerminalQuery $query) {
                            return $query
                                ->andFilterCompare('id', $this->id, '<>')
                                ->andWhere([
                                    'locationId' => ArrayHelper::getValue($this->terminal, 'locationId'),
                                    'warehouseId' => ArrayHelper::getValue($this->terminal, 'warehouseId')
                                ]);
                        },
                        'message' => 'The terminal number must be unique',
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
            'terminal' => $this->_entity->toArray([], ['padlock', 'doorLock']),
        ];
    }

    /**
     *
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Terminal::findOne($this->id) ?: new Terminal();
        $this->_entity->attributes = $this->terminal;
        $this->_entity->createdAt = $this->_entity->createdAt ?: new Expression('now()');

        if ($this->_entity->isAttributeChanged('warehouseId')) {
            $this->_entity->archivedAt = $this->_entity->warehouseId ? new Expression('now()') : null;

            Audit::log($this->_entity, 'warehouseId');
        }

        $locationChanged = $this->_entity->isAttributeChanged('locationId');

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }

        if ($locationChanged) {
            Audit::log($this->_entity, 'locationId');
        }
    }
}