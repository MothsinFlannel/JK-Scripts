<?php

use yii\db\Migration;

/**
 * Class m211031_225419_add_notes
 */
class m211031_225419_add_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoiceItem', 'notes', 'text');
        $this->addColumn('invoiceItem', 'lastLogAt', 'timestamptz(0) null default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211031_225419_add_notes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211031_225419_add_notes cannot be reverted.\n";

        return false;
    }
    */
}
