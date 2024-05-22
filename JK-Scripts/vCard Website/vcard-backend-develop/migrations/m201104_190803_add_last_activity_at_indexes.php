<?php

use yii\db\Migration;

/**
 * Class m201104_190803_add_last_activity_at_indexes
 */
class m201104_190803_add_last_activity_at_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('locationLastActivityAt', 'location', 'lastActivityAt');
        $this->createIndex('terminalLastActivityAt', 'terminal', 'lastActivityAt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_190803_add_last_activity_at_indexes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_190803_add_last_activity_at_indexes cannot be reverted.\n";

        return false;
    }
    */
}
