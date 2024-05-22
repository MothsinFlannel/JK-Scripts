<?php

use yii\db\Migration;

/**
 * Class m220112_130140_add_terminal_notes
 */
class m220112_130140_add_terminal_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('terminal', 'notes', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220112_130140_add_terminal_notes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_130140_add_terminal_notes cannot be reverted.\n";

        return false;
    }
    */
}
