<?php


namespace app\modules\app\scripts\routes;


use app\components\Script;
use app\models\ext\Route;
use Exception;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;

/**
 * Class EditRouteScript
 * @package app\modules\app\routes
 * @property Route $entity
 */
class EditRouteScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var array
     */
    public array $route;

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
            ['route', 'required'],
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
            'route' => $this->_entity->toArray(),
        ];
    }

    /**
     * @return Route|null
     */
    public function getEntity(): ?Route
    {
        return $this->_entity;
    }

    /**
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->_entity = Route::findOne($this->id) ?: new Route();

        $this->_entity->attributes = $this->route;
        if ($this->_entity->isNewRecord) {
            $this->_entity->loadDefaultValues();
        }

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}