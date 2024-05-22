<?php

use yii\db\Migration;

/**
 * Class m230202_125532_add_location_invoicing
 */
class m230202_125532_add_location_invoicing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('drop type if exists "invoicingMode"');
        $this->execute('create type "invoicingMode" as enum(\'automatic\', \'manual\',\'floating\')');
        $this->addColumn('location', 'invoicingMode', '"invoicingMode"');
        $this->update('location', ['invoicingMode' => 'automatic'], ['allowEmptyInvoices' => false]);
        $this->update('location', ['invoicingMode' => 'manual'], ['allowEmptyInvoices' => true]);
        $this->alterColumn('location', 'invoicingMode', '"invoicingMode" not null');
        $this->dropColumn('location', 'allowEmptyInvoices');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230202_125532_add_location_invoicing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230202_125532_add_location_invoicing cannot be reverted.\n";

        return false;
    }
    */
}
