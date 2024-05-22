<?php

use yii\db\Migration;

/**
 * Class m210226_203526_add_disable_screen_lock
 */
class m210226_203526_add_disable_screen_lock extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'disableScreenLock', 'boolean not null default false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_203526_add_disable_screen_lock cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_203526_add_disable_screen_lock cannot be reverted.\n";

        return false;
    }
    */
}
