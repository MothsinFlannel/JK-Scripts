<?php

use yii\db\Migration;

/**
 * Class m230404_092320_add_operates_in_states
 */
class m230404_092320_add_operates_in_states extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'operatesInStates', 'jsonb');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'operatesInStates');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230404_092320_add_operates_in_states cannot be reverted.\n";

        return false;
    }
    */
}
