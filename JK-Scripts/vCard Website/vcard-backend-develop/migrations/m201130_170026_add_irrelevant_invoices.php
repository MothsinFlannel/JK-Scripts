<?php

use yii\db\Migration;

/**
 * Class m201130_170026_add_irrelevant_invoices
 */
class m201130_170026_add_irrelevant_invoices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("alter type invoiceStatus add value 'incomplete'");
    }

    public function up()
    {
        return $this->safeUp();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201130_170026_add_irrelevant_invoices cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201130_170026_add_irrelevant_invoices cannot be reverted.\n";

        return false;
    }
    */
}
