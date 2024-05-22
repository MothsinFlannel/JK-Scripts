<?php

use yii\db\Migration;

/**
 * Class m230217_123042_add_mobile_software
 */
class m230217_123042_add_mobile_software extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('mobileSoftware', [
            'id'   => 'pk',
            'name' => 'varchar(128) not null',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230217_123042_add_mobile_software cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230217_123042_add_mobile_software cannot be reverted.\n";

        return false;
    }
    */
}
