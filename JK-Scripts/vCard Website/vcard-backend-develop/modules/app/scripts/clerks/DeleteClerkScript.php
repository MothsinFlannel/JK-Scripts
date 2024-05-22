<?php


namespace app\modules\app\scripts\clerks;


use app\components\Script;
use app\models\Clerk;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteClerkScript
 * @package app\modules\app\clerks
 */
class DeleteClerkScript extends Script
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
            ['id', ExistValidator::class, 'targetClass' => Clerk::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Clerk::findOne($this->id)->delete();
    }
}