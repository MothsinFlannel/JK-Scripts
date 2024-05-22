<?php

use yii\db\Migration;

/**
 * Class m201024_195204_add_last_activity_at
 */
class m201024_195204_add_last_activity_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('terminal', 'lastActivityAt', 'timestamptz(0)');
        $this->alterColumn('location', 'lastActivityAt', 'timestamptz(0)');

        $this->dropPrimaryKey('pk', 'log');
        $this->addPrimaryKey('pk', 'log', ['createdAt', 'serial', 'terminal', 'isLive']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201024_195204_add_last_activity_at cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201024_195204_add_last_activity_at cannot be reverted.\n";

        return false;
    }
    */
}
