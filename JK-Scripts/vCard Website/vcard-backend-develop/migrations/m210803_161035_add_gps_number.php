<?php

use yii\db\Migration;

/**
 * Class m210803_161035_add_gps_number
 */
class m210803_161035_add_gps_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'gpsNumber', 'varchar(32)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_161035_add_gps_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210803_161035_add_gps_number cannot be reverted.\n";

        return false;
    }
    */
}
