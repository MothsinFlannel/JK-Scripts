<?php


namespace app\modules\reports\scripts\inventory;


use app\models\AuditQuery;
use app\models\ext\Audit;
use app\models\ext\Location;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 * Class PosAuditScript
 * @package app\modules\reports\scripts\inventory
 */
class PosAuditScript extends PagedListScript
{
    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var AuditQuery
     */
    private AuditQuery $query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['query', 'string']
                ],
                'objectify' => true
            ],
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
            'count' => $this->query->count(),
            'results' => ArrayHelper::getColumn($this->query->all(), function (Audit $audit) {
                $logs = Audit::find()
                    ->andWhere([
                        'entity' => Location::tableName(),
                        'attribute' => 'serial',
                        'value' => $audit->value
                    ])
                    ->orderBy('[[createdAt]] desc')
                    ->all();

                if ($removedAt = $this->removedAt($logs)) {
                    $logs = array_merge([
                        new Audit([
                            'id' => null,
                            'entity' => 'location',
                            'identifier' => null,
                            'attribute' => 'serial',
                            'value' => $audit->value,
                            'createdAt' => $removedAt,
                        ])
                    ], $logs);
                }

                return [
                    'device' => $audit->value,
                    'logs' => ArrayHelper::getColumn($logs, function (Audit $log) {
                        return $log->toArray(['createdAt']) + [
                                'location' => Location::findOne($log->identifier)
                                    ?->toArray(['name', 'address', 'state', 'zipCode', 'city'])
                            ];
                    }),
                ];
            }),
        ];
    }

    /**
     * @param array $logs
     * @return string|null
     * @throws Exception
     */
    function removedAt(array $logs): string|null
    {
        $removedAt = null;

        /** @var Audit $lastLog */
        if ($lastLog = ArrayHelper::getValue($logs, 0)) {
            $removedAt = Audit::find()
                ->select('createdAt')
                ->andWhere([
                    'entity' => Location::tableName(),
                    'attribute' => 'serial',
                    'identifier' => $lastLog->identifier,
                ])
                ->andWhere(['<>', 'value', $lastLog->value])
                ->andWhere(['>', 'createdAt', $lastLog->createdAt])
                ->scalar() ?: null;
        }
        return $removedAt;
    }

    /**
     * @return void
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->query = Audit::find()
            ->leftJoin([
                'location' => Location::find()
            ], 'location.id = audit.identifier')
            ->andWhere([
                'and',
                ['entity' => Location::tableName()],
                ['attribute' => 'serial'],
                'length(value) > 0'
            ])
            ->orderBy('[[createdAt]] desc')
            ->offset($this->offset)->limit($this->limit);

        if (is_string($query = @$this->filters->query)) {
            $this->query
                ->andFilterwhere([
                    'or',
                    ['ilike', 'audit.value', $query],
                    ['ilike', 'location.name', $query],
                ]);
        }
    }
}