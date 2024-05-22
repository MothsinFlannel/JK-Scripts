<?php

use yii\db\Migration;

/**
 * Class m201211_225436_add_terminal_index
 */
class m201211_225436_add_terminal_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idxNumber', 'terminal', ['locationId', 'number'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201211_225436_add_terminal_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201211_225436_add_terminal_index cannot be reverted.\n";

        return false;
    }
    */
}
