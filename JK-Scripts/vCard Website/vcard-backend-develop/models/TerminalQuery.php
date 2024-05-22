<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Terminal]].
 *
 * @see Terminal
 */
class TerminalQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @param bool|null $offline
     * @return $this
     */
    public function offline(?bool $offline = true): self
    {
        if ($offline === null) {
            return $this;
        }

        $conditions = [
            // Online
            false => [
                'or',
                '[[location.maxOfflineHours]] = 0',
                '[[terminal.lastActivityAt]] >= now() - ([[location.maxOfflineHours]] || \' hours\')::interval',
                ['isLive' => false]
            ],

            // Offline
            true => [
                'and',
                ['isLive' => true],
                [
                    'or',
                    '[[terminal.lastActivityAt]] is null',
                    [
                        'and',
                        '[[location.maxOfflineHours]] <> 0',
                        '[[terminal.lastActivityAt]] < now() - ([[location.maxOfflineHours]] || \' hours\')::interval',
                    ]
                ]
            ],
        ];

        return $this
            ->andWhere($conditions[$offline]);
    }

    /**
     * {@inheritdoc}
     * @return Terminal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Terminal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $exploded
     * @return $this
     */
    public function filter($query)
    {
        if (empty($query)) {
            return $this;
        }

        $query     = str_replace('#', '', $query);
        $exploded  = explode(' ', $query);
        $condition = [
            'or',
            ['ilike', 'terminal.programName', $exploded],
            ['ilike', 'terminal.groupName', $exploded],
            ['ilike', 'terminal.licenseNumber', $exploded],
            ['ilike', 'terminal.cabinetAssetNumber', $exploded],
            ['ilike', 'terminal.boardAssetNumber', $exploded],
        ];

        if (is_numeric($query)) {
            $condition[] = ['number' => (int)$query];
        }

        return $this->andWhere($condition);
    }
}
