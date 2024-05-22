<?php

namespace app\modules\app\scripts\software;

use app\models\Software;
use vr\core\Script;
use vr\core\validators\ExistValidator;

class GetSoftwareScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var Software|null
     */
    private ?Software $_entity = null;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Software::class]
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
            'software' => $this->_entity->toArray()
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Software::find()->one();
    }
}