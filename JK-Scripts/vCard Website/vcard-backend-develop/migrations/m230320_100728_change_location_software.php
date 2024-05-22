<?php

use yii\db\Migration;

/**
 * Class m230320_100728_change_location_software
 */
class m230320_100728_change_location_software extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('location', 'software');
        $this->delete('software');
        $this->addColumn('software', 'locationId', 'int not null');
        $this->addForeignKey('fkSoftwareLocationId', 'software', 'locationId', 'location', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230320_100728_change_location_software cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230320_100728_change_location_software cannot be reverted.\n";

        return false;
    }
    */
}
