<?php


namespace app\modules\app\scripts\routes;


use app\components\Script;
use app\models\ext\Route;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteRouteScript
 * @package app\modules\app\routes
 */
class DeleteRouteScript extends Script
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
            ['id', ExistValidator::class, 'targetClass' => Route::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Route::findOne($this->id)->delete();
    }
}