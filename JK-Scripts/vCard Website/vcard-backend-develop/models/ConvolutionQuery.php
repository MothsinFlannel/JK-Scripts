<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Convolution]].
 *
 * @see Convolution
 */
class ConvolutionQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @param $since
     * @return self
     */
    public function since($since)
    {
        return $this->andFilterCompare('date', $since, '>=');
    }

    /**
     * @param $until
     * @param bool $includeExact
     * @return self
     */
    public function until($until, bool $includeExact = true): self
    {
        // TODO: check why we need $includeExact param
        return $this->andFilterCompare('date', $until, $includeExact ? '<=' : '<');
    }

    /**
     * @param bool $live
     * @return self
     */
    public function live(bool $live = true): self
    {
        return $this->andWhere(['convolution.isLive' => $live]);
    }

    /**
     * {@inheritdoc}
     * @return Convolution[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Convolution|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
