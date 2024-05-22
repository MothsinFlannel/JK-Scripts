<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m210721_202703_add_invoice_item
 */
class m210721_202703_add_invoice_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('drop type if exists "invoiceItemType"');
        $this->execute('create type "invoiceItemType" as enum(\'automatic\', \'manual\', \'extra\')');

        $this->createTable('invoiceItem', [
            'id'        => 'pk',
            'invoiceId' => 'int not null',
            'number'    => 'varchar(16)',
            'title'     => 'varchar(128)',
            'totalIn'   => 'float8',
            'totalOut'  => 'float8',
            'revenue'   => 'float8',
            'balance'   => 'float8',
            'type'      => '"invoiceItemType" not null default \'automatic\'',
        ]);

        $this->addForeignKey('fkInvoiceItemInvoiceId', 'invoiceItem', 'invoiceId', 'invoice', 'id', 'cascade', 'cascade');

        $extraItems = ActiveRecord::find()->from('extraItem')->asArray();
        foreach ($extraItems->each() as $extraItem) {
            $this->insert('invoiceItem', [
                'invoiceId' => $extraItem['invoiceId'],
                'number'    => 1,
                'title'     => $extraItem['title'],
                'balance'   => $extraItem['amount'],
                'type'      => 'extra'
            ]);
        }

        $this->dropTable('extraItem');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210721_202703_add_invoice_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210721_202703_add_invoice_item cannot be reverted.\n";

        return false;
    }
    */
}
