<?php

use yii\db\Migration;

/**
 * Class m211026_171007_add_last_activity_at_to_convolution
 */
class m211026_171007_add_last_activity_at_to_convolution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('convolution', 'lastLogAt', 'timestamptz(0) null default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211026_171007_add_last_activity_at_to_convolution cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211026_171007_add_last_activity_at_to_convolution cannot be reverted.\n";

        return false;
    }
    */
}
