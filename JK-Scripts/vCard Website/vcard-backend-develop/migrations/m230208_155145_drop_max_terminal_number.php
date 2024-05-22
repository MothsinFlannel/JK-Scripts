<?php

use yii\db\Migration;

/**
 * Class m230208_155145_drop_max_terminal_number
 */
class m230208_155145_drop_max_terminal_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('location', 'maxTerminalNumber');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230208_155145_drop_max_terminal_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230208_155145_drop_max_terminal_number cannot be reverted.\n";

        return false;
    }
    */
}
