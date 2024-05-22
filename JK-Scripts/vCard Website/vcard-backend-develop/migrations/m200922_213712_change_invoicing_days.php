<?php

use yii\db\Migration;

/**
 * Class m200922_213712_change_invoicing_days
 */
class m200922_213712_change_invoicing_days extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('location', 'invoicingDays', 'varchar(128) not null default \'Monday\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_213712_change_invoicing_days cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_213712_change_invoicing_days cannot be reverted.\n";

        return false;
    }
    */
}
