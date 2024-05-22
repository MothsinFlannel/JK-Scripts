<?php

use yii\db\Migration;

/**
 * Class m210322_155435_add_extra_items
 */
class m210322_155435_add_extra_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('extraItem', [
            'id'        => 'pk',
            'invoiceId' => 'int',
            'title'     => 'varchar(128) not null',
            'amount'    => 'double',
        ]);

        $this->addForeignKey('fkExtraItemInvoiceId', 'extraItem', 'invoiceId', 'invoice', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210322_155435_add_extra_items cannot be reverted.\n";

        return false;
    }
    */
}
