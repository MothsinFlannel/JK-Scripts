<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Route]].
 *
 * @see Route
 */
class RouteQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @return self
     */
    public function accessible(): self
    {
        return $this;
    }

    /**
     * @param string|null $query
     * @return $this
     */
    public function filter(?string $query): self
    {
        if (empty($query)) {
            return $this;
        }

        $query = explode(' ', $query);

        return $this
            ->andWhere(
                ['ilike', 'name', $query],
            );
    }

    /**
     * {@inheritdoc}
     * @return Route[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Route|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
