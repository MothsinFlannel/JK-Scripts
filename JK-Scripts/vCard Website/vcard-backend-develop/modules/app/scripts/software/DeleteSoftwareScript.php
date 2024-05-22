<?php

namespace app\modules\app\scripts\software;

use app\components\Script;
use app\models\Software;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

class DeleteSoftwareScript extends Script
{
    /**
     * @var int
     */
    public int $id;

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
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Software::findOne($this->id)->delete();
    }
}