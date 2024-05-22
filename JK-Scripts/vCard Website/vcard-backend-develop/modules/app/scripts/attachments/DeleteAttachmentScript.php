<?php

namespace app\modules\app\scripts\attachments;

use app\models\ext\Attachment;
use Throwable;
use vr\core\Script;
use yii\db\StaleObjectException;

/**
 *
 */
class DeleteAttachmentScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $id = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', 'exist', 'targetClass' => Attachment::class]
        ];
    }

    /**
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Attachment::findOne($this->id)->delete();
    }
}