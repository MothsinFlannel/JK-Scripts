<?php

namespace app\modules\app\scripts\software;

use app\models\ext\Location;
use app\models\Software;
use app\models\SoftwareQuery;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 * Class SoftwareListScript
 * @package app\modules\app\software
 */
class SoftwareListScript extends PagedListScript
{
    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var SoftwareQuery
     */
    protected SoftwareQuery $_query;

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
                    ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
                    ['offline', 'boolean'],
                ],
                'objectify' => true,
            ]
        ];
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
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Software $software) {
                return $software->toArray();
            })
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {

        $this->_query = Software::find()
            ->andWhere([
                'locationId' => $this->locationId,
            ])
            ->offset($this->offset)->limit($this->limit);

        $this->applySortingToQuery($this->_query);
    }
}