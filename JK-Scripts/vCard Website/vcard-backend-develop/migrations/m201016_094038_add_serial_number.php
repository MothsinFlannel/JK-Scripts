<?php

use yii\db\Migration;

/**
 * Class m201016_094038_add_serial_number
 */
class m201016_094038_add_serial_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('terminal', 'serialNumber', 'varchar(32)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201016_094038_add_serial_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201016_094038_add_serial_number cannot be reverted.\n";

        return false;
    }
    */
}
