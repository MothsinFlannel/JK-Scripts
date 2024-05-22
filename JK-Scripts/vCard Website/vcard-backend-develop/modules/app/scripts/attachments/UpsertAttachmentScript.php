<?php

namespace app\modules\app\scripts\attachments;

use app\models\ext\Attachment;
use ReflectionException;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\upload\sources\Base64Source;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 *
 */
class UpsertAttachmentScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $id = null;

    /**
     * @var array
     */
    public array $attachment = [];

    /**
     * @var string|null
     */
    public ?string $file = null;

    /**
     * @var Attachment|null
     */
    private ?Attachment $entity = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['attachment', 'required'],
            ['file', 'required', 'when' => fn() => !$this->id],
            ['id', 'exist', 'targetClass' => Attachment::class]
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
            'attachment' => $this->entity
        ];
    }

    /**
     * @return void
     * @throws ErrorsException
     * @throws ReflectionException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    protected function onExecute(): void
    {
        $this->entity = Attachment::findOne($this->id) ?: new Attachment();
        $this->entity->attributes = $this->attachment;

        if ($this->file) {
            $this->entity->upload('file', new Base64Source([
                'data' => $this->file
            ]), [
                'extension' => ArrayHelper::getValue($this->attributes, 'type'),
            ]);
        }

        if (!$this->entity->save() || !$this->entity->refresh()) {
            throw new ErrorsException($this->entity->errors);
        }
    }
}