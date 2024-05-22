<?php

use yii\db\Migration;

/**
 * Class m230125_134019_add_location_fields
 */
class m230125_134019_add_location_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'installedAt', 'datetime');
        $this->addColumn('location', 'serviceRequest', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230125_134019_add_location_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230125_134019_add_location_fields cannot be reverted.\n";

        return false;
    }
    */
}
