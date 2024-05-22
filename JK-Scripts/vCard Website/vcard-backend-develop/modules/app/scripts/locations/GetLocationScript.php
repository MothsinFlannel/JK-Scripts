<?php


namespace app\modules\app\scripts\locations;


use app\models\ext\Location;
use Throwable;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use yii\db\Expression;

/**
 * Class GetLocationScript
 * @package app\modules\app\locations
 */
class GetLocationScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var Location|null
     */
    private ?Location $_entity;

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
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'location' => $this->_entity->toArray([], [
                'moneyIn',
                'moneyOut',
                'revenue',
                'profit',
                'due',
                'paid',
                'operatorEmail',
                'attachments',
                'route'
            ]),
        ];
    }

    /**
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_entity = Location::find()
            ->select([
                'location.*',
                'lastActivityAt' => new Expression("timezone(timezone, [[lastActivityAt]])")
            ])
            ->andWhere(['id' => $this->id])
            ->one();
    }
}