<?php

namespace app\models\ext;

use vr\core\ArrayHelper;
use vr\upload\ActiveBinaryTrait;
use vr\upload\BinaryBehavior;
use vr\upload\connectors\FileSystemDataConnector;

/**
 *
 */
class Attachment extends \app\models\Attachment
{
    use ActiveBinaryTrait;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'binary' => [
                'class' => BinaryBehavior::class,
                'binaryAttributes' => [
                    'file' => [
                        'template' => '{base}-{datetime}-{tag}',
                        'connector' => [
                            'class' => FileSystemDataConnector::class,
                        ],
                    ]
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        ArrayHelper::setValue($fields, 'file', fn() => $this->url('file'));

        return $fields;
    }

    /**
     * @return array
     */
    public function safeAttributes(): array
    {
        $attributes = parent::safeAttributes();

        ArrayHelper::removeValue($attributes, 'createdAt');
        ArrayHelper::removeValue($attributes, 'file');
        ArrayHelper::removeValue($attributes, 'type');

        return $attributes;
    }
}