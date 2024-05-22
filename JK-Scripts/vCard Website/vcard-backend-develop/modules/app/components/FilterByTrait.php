<?php

namespace app\modules\app\components;

use yii\db\ActiveQuery;

trait FilterByTrait
{
    /**
     * @param ActiveQuery $query
     * @param string $attribute
     * @param string|array|null $value
     * @return void
     */
    private function filterBy(ActiveQuery $query, string $attribute, string|array|null $value): void
    {
        if (is_string($value)) {
            $value = [$value];
        }

        if (!empty($value)) {
            $conditions = [];

            foreach ($value as $item) {
                $item = explode(' ', $item);
                $conditions[] = ['ilike', $attribute, $item];
            }

            $query->andWhere(array_merge(['or'], $conditions));
        }
    }
}