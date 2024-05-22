<?php


namespace app\modules\app\scripts\installations;


use app\models\Installation;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetInstallationScript
 * @package app\modules\app\scripts\installations
 */
class GetInstallationScript extends Script
{
    /**
     * @var int|null
     */
    public int|null $id = null;

    /**
     * @var Installation|null
     */
    private ?Installation $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Installation::class]
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
            'installation' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Installation::findOne($this->id);
    }
}