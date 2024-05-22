<?php

use yii\db\Migration;

/**
 * Class m201104_111610_add_terminal_numbers
 */
class m201104_111610_add_terminal_numbers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('terminal', 'serialNumber', 'cabinetAssetNumber');
        $this->addColumn('terminal', 'boardAssetNumber', 'varchar(32)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_111610_add_terminal_numbers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_111610_add_terminal_numbers cannot be reverted.\n";

        return false;
    }
    */
}
