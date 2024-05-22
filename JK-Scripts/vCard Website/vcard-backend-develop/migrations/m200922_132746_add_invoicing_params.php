<?php

use yii\db\Migration;

/**
 * Class m200922_132746_add_invoicing_params
 */
class m200922_132746_add_invoicing_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("drop type if exists invoicingPeriod");
        $this->execute("create type invoicingPeriod as enum('week', 'month')");

        $this->addColumn('location', 'invoicingPeriod', 'invoicingPeriod not null default \'week\'');
        $this->addColumn('location', 'invoicingDays', 'varchar array not null default \'{Monday}\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_132746_add_invoicing_params cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_132746_add_invoicing_params cannot be reverted.\n";

        return false;
    }
    */
}
