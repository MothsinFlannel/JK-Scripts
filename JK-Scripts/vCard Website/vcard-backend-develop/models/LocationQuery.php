<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Location]].
 *
 * @see Location
 */
class LocationQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @param bool $active
     * @return LocationQuery
     */
    public function active(bool $active = true): self
    {
        return $this->andWhere(['location.isActive' => $active]);
    }

    /**
     * @param bool $live
     * @return LocationQuery
     */
    public function live(bool $live = true): self
    {
        return $this->andWhere(['location.isLive' => $live]);
    }

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
                '[[location.lastActivityAt]] >= now() - ([[maxOfflineHours]] || \' hours\')::interval',
                ['isLive' => false]
            ],

            // Offline
            true => [
                'and',
                ['isLive' => true],
                [
                    'or',
                    '[[location.lastActivityAt]] is null',
                    [
                        'and',
                        '[[location.maxOfflineHours]] <> 0',
                        '[[location.lastActivityAt]] < now() - ([[maxOfflineHours]] || \' hours\')::interval',
                    ]
                ]
            ],
        ];

        return $this->andWhere($conditions[$offline]);
    }

    /**
     * @param $query
     * @return $this
     */
    public function filter($query): self
    {
        if (empty($query)) {
            return $this;
        }

        $query        = explode(' ', $query);
        $softwareJoin = uniqid();

        $softwareQuery = Software::find()
            ->select('locationId')
            ->andWhere([
                'or',
                ['ilike', 'name', $query],
            ])
            ->groupBy('locationId');
        return $this
            ->leftJoin([
                $softwareJoin => $softwareQuery
            ], "[[$softwareJoin.locationId]] = [[location.id]]")
            ->andWhere(
                [
                    'or',
                    ['ilike', 'city', $query],
                    ['ilike', 'state', $query],
                    ['ilike', 'location.name', $query],
                    ['ilike', 'location.serial', $query],
                    ['ilike', 'location.address', $query],
                    ['ilike', 'location.state', $query],
                    ['ilike', 'location.zipCode', $query],
                    ['ilike', 'location.contactPhone', $query],
                    "[[$softwareJoin.locationId]] is not null"
                ]
            );
    }

    /**
     * {@inheritdoc}
     * @return ext\Location[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ext\Location|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
