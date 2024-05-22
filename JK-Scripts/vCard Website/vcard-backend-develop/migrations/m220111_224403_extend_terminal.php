<?php

use yii\db\Migration;

/**
 * Class m220111_224403_extend_terminal
 */
class m220111_224403_extend_terminal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('terminal', 'placementDate', 'date');
        $this->addColumn('terminal', 'refillDate', 'date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220111_224403_extend_terminal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220111_224403_extend_terminal cannot be reverted.\n";

        return false;
    }
    */
}
