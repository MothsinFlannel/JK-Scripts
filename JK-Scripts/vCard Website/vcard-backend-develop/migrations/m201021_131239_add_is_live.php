<?php

use yii\db\Migration;

/**
 * Class m201021_131239_add_is_live
 */
class m201021_131239_add_is_live extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'isLive', 'boolean not null default true');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201021_131239_add_is_live cannot be reverted.\n";

        return false;
    }
    */
}
