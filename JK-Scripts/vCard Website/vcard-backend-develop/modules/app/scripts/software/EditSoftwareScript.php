<?php

namespace app\modules\app\scripts\software;

use app\models\Software;
use app\models\SoftwareQuery;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 *
 */
class EditSoftwareScript extends Script
{
    /**
     * @var array
     */
    public array $software = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var Software|null
     */
    private ?Software $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['software', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Software::class],
            [
                'software',
                NestedValidator::class,
                'rules' => [
                    [
                        'number',
                        'unique',
                        'targetClass' => Software::class,
                        'targetAttribute' => 'number',
                        'filter' => function (SoftwareQuery $query) {
                            return $query
                                ->andFilterCompare('id', $this->id, '<>')
                                ->andWhere([
                                    'locationId' => ArrayHelper::getValue($this->software, 'locationId'),
                                    'warehouseId' => ArrayHelper::getValue($this->software, 'warehouseId')
                                ]);
                        },
                        'message' => 'The software number must be unique',
                    ]
                ],
            ]
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
            'software' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Software::findOne($this->id) ?: new Software();
        $this->_entity->attributes = $this->software;

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}