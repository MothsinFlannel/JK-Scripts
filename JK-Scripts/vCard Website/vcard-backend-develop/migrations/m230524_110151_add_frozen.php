<?php

use yii\db\Migration;

/**
 * Class m230524_110151_add_frozen
 */
class m230524_110151_add_frozen extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('software', 'isFrozen', 'boolean not null default false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230524_110151_add_frozen cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230524_110151_add_frozen cannot be reverted.\n";

        return false;
    }
    */
}
