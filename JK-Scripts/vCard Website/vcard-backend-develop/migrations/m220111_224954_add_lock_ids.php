<?php

use yii\db\Migration;

/**
 * Class m220111_224954_add_lock_ids
 */
class m220111_224954_add_lock_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('terminal', 'padlockId', 'int null default null');
        $this->addColumn('terminal', 'doorLockId', 'int null default null');

        $this->addForeignKey('fkTerminalPadlockId', 'terminal', 'padlockId', 'padlock', 'id', 'set null', 'cascade');
        $this->addForeignKey('fkTerminalDoorLockId', 'terminal', 'doorLockId', 'doorLock', 'id', 'set null', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220111_224954_add_lock_ids cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220111_224954_add_lock_ids cannot be reverted.\n";

        return false;
    }
    */
}
