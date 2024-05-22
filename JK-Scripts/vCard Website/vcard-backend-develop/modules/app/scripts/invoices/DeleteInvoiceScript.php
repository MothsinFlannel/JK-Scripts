<?php

namespace app\modules\app\scripts\invoices;

use app\models\ext\Invoice;
use Throwable;
use vr\core\Script;

/**
 *
 */
class DeleteInvoiceScript extends Script
{
    /**
     * @var int|array
     */
    public int|array $id = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', 'exist', 'targetClass' => Invoice::class, 'targetAttribute' => 'id', 'allowArray' => true],
        ];
    }

    /**
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $query = Invoice::find()->andWhere(['id' => $this->id]);

        foreach ($query->each() as $each) {
            $each->delete();
        }
    }
}