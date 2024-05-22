<?php

namespace app\modules\app\scripts\audit;

use app\components\Script;
use app\models\AuditQuery;
use app\models\ext\Audit;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 *
 */
class AuditByAttributeScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $attribute = null;

    /**
     * @var string|null
     */
    public ?string $value = null;

    private AuditQuery $query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['attribute', 'value'], 'required'],
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
            'results' => ArrayHelper::getColumn($this->query->all(), function (Audit $audit) {
                return $audit->toArray() + [
                        $audit->entity => ActiveRecord::find()
                            ->from($audit->entity)
                            ->andWhere(['id' => $audit->identifier])  // TODO: potentially risky place
                            ->asArray()
                            ->one(),
                    ];
            }),
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {
        $this->query = Audit::find()
            ->andWhere([
                'attribute' => $this->attribute,
                'value' => $this->value
            ])
            ->orderBy('[[createdAt]] desc');
    }
}