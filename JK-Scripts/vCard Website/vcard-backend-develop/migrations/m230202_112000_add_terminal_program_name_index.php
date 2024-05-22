<?php

use yii\db\Migration;

/**
 * Class m230202_112000_add_terminal_program_name_index
 */
class m230202_112000_add_terminal_program_name_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idxTerminalProgramName', 'terminal', 'programName');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230202_112000_add_terminal_program_name_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230202_112000_add_terminal_program_name_index cannot be reverted.\n";

        return false;
    }
    */
}
