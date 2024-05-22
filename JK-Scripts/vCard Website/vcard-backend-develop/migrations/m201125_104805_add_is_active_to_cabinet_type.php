<?php

use yii\db\Migration;

/**
 * Class m201125_104805_add_is_active_to_cabinet_type
 */
class m201125_104805_add_is_active_to_cabinet_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cabinetType', 'isActive', 'boolean not null default true');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_104805_add_is_active_to_cabinet_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_104805_add_is_active_to_cabinet_type cannot be reverted.\n";

        return false;
    }
    */
}
