<?php

use yii\db\Migration;

/**
 * Class m230208_162953_add_is_mobile_flag_to_game_titles
 */
class m230208_162953_add_is_mobile_flag_to_game_titles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('game', 'isMobile', 'boolean not null default false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230208_162953_add_is_mobile_flag_to_game_titles cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230208_162953_add_is_mobile_flag_to_game_titles cannot be reverted.\n";

        return false;
    }
    */
}
