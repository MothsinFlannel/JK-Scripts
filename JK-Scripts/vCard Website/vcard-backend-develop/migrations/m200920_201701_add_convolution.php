<?php

use yii\db\Migration;

/**
 * Class m200920_201701_add_convolution
 */
class m200920_201701_add_convolution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('convolutionDate', 'convolution', 'date');

        $this->createIndex('invoiceSince', 'invoice', 'since');
        $this->createIndex('invoiceUntil', 'invoice', 'until');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200920_201701_add_convolution cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200920_201701_add_convolution cannot be reverted.\n";

        return false;
    }
    */
}
