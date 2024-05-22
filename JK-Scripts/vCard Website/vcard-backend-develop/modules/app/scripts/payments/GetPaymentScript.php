<?php


namespace app\modules\app\scripts\payments;


use app\models\ext\Payment;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetPaymentScript
 * @package app\modules\app\payments
 */
class GetPaymentScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var Payment|null
     */
    private ?Payment $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Payment::class]
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
            'payment' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Payment::findOne($this->id);
    }
}