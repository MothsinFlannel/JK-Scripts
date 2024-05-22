<?php

namespace app\modules\app\scripts\locations;

use app\models\ext\Location;
use Throwable;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use Yii;
use yii\base\InvalidConfigException;

/**
 *
 */
class ServiceRequestLocationScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var string|null
     */
    public ?string $note = null;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            [['id', 'note'], 'required'],
            ['id', ExistValidator::class, 'targetClass' => Location::class],
            ['note', 'trim']
        ];
    }

    /**
     * @throws ErrorsException
     * @throws Throwable
     * @throws InvalidConfigException
     */
    protected function onExecute(): void
    {
        $location = Location::findOne($this->id);
        Yii::$app->get('sendgrid')->serviceRequest($location, $this->note);
    }
}