<?php

use yii\db\Migration;

/**
 * Class m230202_110903_add_mobile_software
 */
class m230202_110903_add_mobile_software extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'mobileSoftware', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230202_110903_add_mobile_software cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230202_110903_add_mobile_software cannot be reverted.\n";

        return false;
    }
    */
}
