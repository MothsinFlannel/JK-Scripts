<?php

use yii\db\Migration;

/**
 * Class m210712_180123_add_location_license_number
 */
class m210712_180123_add_location_license_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'licenseNumber', 'varchar(256)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210712_180123_add_location_license_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210712_180123_add_location_license_number cannot be reverted.\n";

        return false;
    }
    */
}
