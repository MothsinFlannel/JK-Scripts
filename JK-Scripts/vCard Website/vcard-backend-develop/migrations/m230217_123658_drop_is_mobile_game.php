<?php

use yii\db\Migration;

/**
 * Class m230217_123658_drop_is_mobile_game
 */
class m230217_123658_drop_is_mobile_game extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('game', 'isMobile');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230217_123658_drop_is_mobile_game cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230217_123658_drop_is_mobile_game cannot be reverted.\n";

        return false;
    }
    */
}
