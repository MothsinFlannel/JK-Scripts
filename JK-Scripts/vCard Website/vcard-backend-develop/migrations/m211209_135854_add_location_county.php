<?php

use yii\db\Migration;

/**
 * Class m211209_135854_add_location_county
 */
class m211209_135854_add_location_county extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'county', 'varchar(128) null default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211209_135854_add_location_county cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211209_135854_add_location_county cannot be reverted.\n";

        return false;
    }
    */
}
