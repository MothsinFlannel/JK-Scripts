<?php


namespace app\modules\app\scripts\categories;


use app\components\ExportQueryTrait;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class ExportCategoriesScript
 * @package app\modules\app\scripts\categories
 */
class ExportCategoriesScript extends CategoriesListScript
{
    use ExportQueryTrait;

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws RangeNotSatisfiableHttpException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $this->queryToCsv($this->_query);

        return [];
    }
}