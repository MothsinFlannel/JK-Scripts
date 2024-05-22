<?php


namespace app\modules\app\scripts\installations;


use app\models\Installation;
use app\models\InstallationQuery;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;

/**
 * Class InstallationListScript
 * @package app\modules\app\scripts\installations
 */
class InstallationListScript extends PagedListScript
{
    /**
     * @var InstallationQuery
     */
    protected InstallationQuery $_query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array|string[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Installation $installation) {
                return $installation->toArray();
            })
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_query = Installation::find()
            ->andWhere('[[handledAt]] is null')
            ->offset($this->offset)->limit($this->limit);
    }
}