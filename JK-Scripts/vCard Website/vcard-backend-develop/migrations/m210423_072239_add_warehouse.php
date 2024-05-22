<?php

use yii\db\Migration;

/**
 * Class m210423_072239_add_warehouse
 */
class m210423_072239_add_warehouse extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('warehouse', [
            'id'   => 'pk',
            'name' => 'varchar(128) not null',
        ]);

        $this->alterColumn('terminal', 'locationId', 'int null default null');
        $this->addColumn('terminal', 'warehouseId', 'int null default null');
        $this->addColumn('terminal', 'archivedAt', 'datetime null default null');

        $this->addForeignKey('fkTerminalWarehouseId', 'terminal', 'warehouseId', 'warehouse', 'id', 'set null', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210423_072239_add_warehouse cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_072239_add_warehouse cannot be reverted.\n";

        return false;
    }
    */
}
