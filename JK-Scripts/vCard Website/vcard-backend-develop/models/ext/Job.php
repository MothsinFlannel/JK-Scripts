<?php

namespace app\models\ext;

use app\models\UserQuery;
use Yii;

/**
 * @property User $initiator
 */
class Job extends \app\models\Job
{
    /**
     *
     */
    const STATUS_PENDING = 'pending';

    /**
     *
     */
    const STATUS_IN_PROGRESS = 'in-progress';

    /**
     *
     */
    const STATE_SUCCEEDED = 'succeeded';

    /**
     *
     */
    const STATE_FAILED = 'failed';

    /**
     *
     */
    const STATE_CANCELED = 'canceled';

    /**
     *
     */
    const CACHE_DURATION = 5;

    /**
     * @return string[]
     */
    public function extraFields(): array
    {
        return [
            'initiator'
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        if ($this->state == self::STATE_SUCCEEDED) {
            $fields = array_merge($fields, [
                'output' => function () {
                    return Yii::$app->urlManager->createAbsoluteUrl("/$this->output");
                }
            ]);
        }

        return $fields;
    }

    /**
     * @return UserQuery
     */
    public function getInitiator(): UserQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasOne(User::class, ['id' => 'initiatorId']);
    }
}