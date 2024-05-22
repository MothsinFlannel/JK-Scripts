<?php


namespace app\modules\app\scripts\terminals;


use app\models\AuditQuery;
use app\models\ext\Audit;
use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\ext\Warehouse;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use yii\db\ActiveRecordInterface;

/**
 * Class TerminalMovementsScript
 * @package app\modules\app\scripts\terminals
 */
class TerminalMovementsScript extends PagedListScript
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var AuditQuery
     */
    private AuditQuery $_query;

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
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
            'count' => $this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Audit $audit) {
                $class = ArrayHelper::getValue([
                    'locationId' => Location::class,
                    'warehouseId' => Warehouse::class,
                ], $audit->attribute);

                if (!$audit->value) { // That means moving from warehouse to location
                    $class = Location::class;

                    // Find the previous movement to location
                    $previous = Audit::find()
                        ->andWhere([
                            'entity' => Terminal::tableName(),
                            'identifier' => $this->id,
                            'attribute' => 'locationId'
                        ])
                        ->andWhere('[[audit.createdAt]] < :timestamp', [':timestamp' => $audit->createdAt])
                        ->orderBy('[[audit.createdAt]] desc')
                        ->one();

                    $audit->value = ArrayHelper::getValue($previous, 'value')
                        ?: Terminal::findOne($this->id)->locationId;
                }

                /** @var ActiveRecordInterface $object */
                $object = call_user_func([$class, 'findOne'], $audit->value);

                return [
                    'id' => (int)$audit->value,
                    'name' => $object->getAttribute('name'),
                    'type' => $object::tableName(),
                    'createdAt' => $audit->createdAt,
                ];
            })
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {
        $this->_query = Audit::find()
            ->andWhere([
                'entity' => Terminal::tableName(),
                'identifier' => $this->id,
                'attribute' => ['warehouseId', 'locationId'],
            ])
            ->orderBy('createdAt desc')
            ->offset($this->offset)->limit($this->limit);
    }
}