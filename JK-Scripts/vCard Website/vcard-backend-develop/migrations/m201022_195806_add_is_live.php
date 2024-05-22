<?php

use yii\db\Migration;

/**
 * Class m201022_195806_add_is_live
 */
class m201022_195806_add_is_live extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('log', 'isLive', 'boolean not null default true');
        $this->addColumn('convolution', 'isLive', 'boolean not null default true');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_195806_add_is_live cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_195806_add_is_live cannot be reverted.\n";

        return false;
    }
    */
}
