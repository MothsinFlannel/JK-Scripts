<?php

use yii\db\Migration;

/**
 * Class m201015_141416_add_machine_type
 */
class m201015_141416_add_machine_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('machineType', [
            'id'   => 'pk',
            'name' => 'varchar(64)',
        ]);

        $this->addColumn('terminal', 'machineTypeId', 'int');
        $this->addColumn('terminal', 'licenseNumber', 'varchar(32)');

        $this->addForeignKey('fkTerminalMachineType', 'terminal', 'machineTypeId', 'machineType', 'id', 'set null', 'cascade');

        $this->insert('machineType', [
            'name' => 'Fish Game'
        ]);

        $this->insert('machineType', [
            'name' => 'Redemption'
        ]);

        $this->insert('machineType', [
            'name' => 'Software'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fkTerminalMachineType', 'terminal');
        $this->dropTable('machineType');
        $this->dropColumn('terminal', 'machineTypeId');
        $this->dropColumn('terminal', 'licenseNumber');

        return null;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201015_141416_add_machine_type cannot be reverted.\n";

        return false;
    }
    */
}
