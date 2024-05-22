<?php

use yii\db\Migration;

/**
 * Class m221107_165202_add_contact_name
 */
class m221107_165202_add_contact_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'contactName', 'varchar(256)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221107_165202_add_contact_name cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221107_165202_add_contact_name cannot be reverted.\n";

        return false;
    }
    */
}
