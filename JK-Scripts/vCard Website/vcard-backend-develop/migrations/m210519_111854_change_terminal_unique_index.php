<?php

use yii\db\Migration;

/**
 * Class m210519_111854_change_terminal_unique_index
 */
class m210519_111854_change_terminal_unique_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('idxNumber', 'terminal');
        $this->createIndex('idxNumber', 'terminal', [
            'locationId',
            'number',
            'warehouseId'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210519_111854_change_terminal_unique_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210519_111854_change_terminal_unique_index cannot be reverted.\n";

        return false;
    }
    */
}
