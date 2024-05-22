<?php

use yii\db\Migration;

/**
 * Class m210629_095831_add_list
 */
class m210629_095831_add_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('game', [
            'id'    => 'pk',
            'name' => 'varchar(128) not null',
        ]);

        $this->createTable('padlock', [
            'id'    => 'pk',
            'name' => 'varchar(128) not null',
        ]);

        $this->createTable('doorLock', [
            'id'    => 'pk',
            'name' => 'varchar(128) not null',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210629_095831_add_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210629_095831_add_list cannot be reverted.\n";

        return false;
    }
    */
}
