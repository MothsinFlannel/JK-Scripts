<?php

use yii\db\Migration;

/**
 * Class m230404_083054_drop_floating_invoicing_mode
 */
class m230404_083054_drop_floating_invoicing_mode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('location', 'invoicingMode', 'varchar(32)');
        $this->update('location', ['invoicingMode' => 'blank'], ['invoicingMode' => 'manual']);
        $this->update('location', ['invoicingMode' => 'custom'], ['invoicingMode' => 'floating']);

        $this->execute('drop type if exists "invoicingMode"');
        $this->execute('create type "invoicingMode" as enum(\'automatic\', \'custom\',\'blank\')');
        $this->alterColumn('location', 'invoicingMode', '"invoicingMode" not null USING "invoicingMode"::"invoicingMode"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230404_083054_drop_floating_invoicing_mode cannot be reverted.\n";

        return false;
    }
    */
}
