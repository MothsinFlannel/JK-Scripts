<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Company]].
 *
 * @see Company
 */
class CompanyQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @param $query
     * @return $this
     */
    public function filter($query)
    {
        if (empty($query = explode(' ', $query))) {
            return $this;
        }

        return $this
            ->andWhere([
                'or',
                ['ilike', 'name', $query],
                ['ilike', 'contactName', $query],
                ['ilike', 'contactEmail', $query],
            ]);
    }

    /**
     * {@inheritdoc}
     * @return Company[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Company|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
