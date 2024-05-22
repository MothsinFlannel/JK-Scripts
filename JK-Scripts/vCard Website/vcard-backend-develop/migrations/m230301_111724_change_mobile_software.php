<?php

use yii\db\Migration;

/**
 * Class m230301_111724_change_mobile_software
 */
class m230301_111724_change_mobile_software extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('location', 'mobileSoftware');
        $this->addColumn('location', 'mobileSoftware', 'varchar[]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230301_111724_change_mobile_software cannot be reverted.\n";

        return false;
    }
    */
}
