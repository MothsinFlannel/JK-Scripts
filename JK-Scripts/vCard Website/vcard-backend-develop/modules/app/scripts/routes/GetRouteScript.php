<?php


namespace app\modules\app\scripts\routes;


use app\models\ext\Route;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetRouteScript
 * @package app\modules\app\routes
 */
class GetRouteScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var Route|null
     */
    private ?Route $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Route::class]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'route' => $this->_entity->toArray([], ['locations', 'locationsCount']),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Route::findOne($this->id);
    }
}