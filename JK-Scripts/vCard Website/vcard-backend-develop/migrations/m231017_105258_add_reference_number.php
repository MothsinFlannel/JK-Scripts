<?php

use yii\db\Migration;

/**
 * Class m231017_105258_add_reference_number
 */
class m231017_105258_add_reference_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('terminal', 'referenceNumber', 'varchar(64)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('terminal', 'referenceNumber');
    }
}
