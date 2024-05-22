<?php


namespace app\modules\app\scripts\installations;


use app\components\Script;
use app\models\Installation;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteInstallationScript
 * @package app\modules\app\scripts\installations
 */
class DeleteInstallationScript extends Script
{
    /**
     * @var int|null
     */
    public int|null $id = null;

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
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Installation::findOne($this->id)->delete();
    }
}