<?php

use yii\db\Migration;

/**
 * Class m221207_200647_add_allow_empty_invoices
 */
class m221207_200647_add_allow_empty_invoices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'allowEmptyInvoices', 'boolean not null default false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221207_200647_add_allow_empty_invoices cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221207_200647_add_allow_empty_invoices cannot be reverted.\n";

        return false;
    }
    */
}
