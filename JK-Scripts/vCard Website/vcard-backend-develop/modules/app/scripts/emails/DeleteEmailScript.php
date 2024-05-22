<?php


namespace app\modules\app\scripts\emails;


use app\components\Script;
use app\models\NotificationEmail;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteEmailScript
 * @package app\modules\app\emails
 */
class DeleteEmailScript extends Script
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
            ['id', ExistValidator::class, 'targetClass' => NotificationEmail::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        NotificationEmail::findOne($this->id)->delete();
    }
}