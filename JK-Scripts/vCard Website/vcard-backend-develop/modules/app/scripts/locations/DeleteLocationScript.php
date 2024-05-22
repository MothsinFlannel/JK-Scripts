<?php


namespace app\modules\app\scripts\locations;


use app\components\Script;
use app\models\ext\Location;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteLocationScript
 * @package app\modules\app\locations
 */
class DeleteLocationScript extends Script
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
            ['id', ExistValidator::class, 'targetClass' => Location::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Location::findOne($this->id)->delete();
    }
}