<?php

use yii\db\Migration;

/**
 * Class m230320_091355_create_software_titles
 */
class m230320_091355_create_software_titles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('softwareTitle', [
            'id' => 'pk',
            'name' => 'varchar(128) not null',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230320_091355_create_software_titles cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230320_091355_create_software_titles cannot be reverted.\n";

        return false;
    }
    */
}
