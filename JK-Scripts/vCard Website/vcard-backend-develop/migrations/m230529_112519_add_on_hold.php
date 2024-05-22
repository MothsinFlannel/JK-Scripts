<?php

use yii\db\Migration;

/**
 * Class m230529_112519_add_on_hold
 */
class m230529_112519_add_on_hold extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'onHold', 'boolean not null default false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230529_112519_add_on_hold cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230529_112519_add_on_hold cannot be reverted.\n";

        return false;
    }
    */
}
