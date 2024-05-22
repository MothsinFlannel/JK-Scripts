<?php

namespace app\modules\reports\scripts\financial;

use app\components\Script;
use app\models\ext\Location;
use app\models\Log;
use yii\db\Expression;

/**
 *
 */
class UninvoicedReportScript extends Script
{
    public ?string $since;

    public ?string $until;

    /**
     * @var array
     */
    private array $results;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['since', 'until'], 'required'],
            [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'results' => $this->results
        ];
    }

    /**
     * @return void
     * @throws \Throwable
     */
    protected function onExecute(): void
    {
        $logQuery = Log::find()
            ->andWhere([
                'and',
                ['>=', new Expression('date([[createdAt]])'), $this->since],
                ['<=', new Expression('date([[createdAt]])'), $this->until],
                ['>', new Expression('date([[receivedAt]])'), $this->until],
            ]);

        $this->results = Location::find()
            ->select([
                'location.serial',
                'location.name',
                'log.terminal',
                'moneyIn' => 'sum([[moneyIn]])',
                'moneyOut' => 'sum([[moneyOut]])'
            ])
            ->leftJoin([
                'log' => $logQuery
            ], 'log.serial = location.serial')
            ->leftJoin('invoice', '[[invoice.locationId]] = [[location.id]]')
            ->andWhere([
                'and',
                ['>', 'receivedAt', new Expression('[[invoice.createdAt]]')],
                ['invoice.since' => $this->since],
            ])
            ->groupBy([
                'location.name',
                'location.serial',
                'terminal'
            ])
            ->asArray()
            ->all();
    }
}