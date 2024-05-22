<?php

use yii\db\Migration;

/**
 * Class m230303_095037_extend_mobile_software
 */
class m230303_095037_extend_mobile_software extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('mobileSoftware', 'software');

        $this->addColumn('software', 'serverNo', 'varchar(16)');
        $this->addColumn('software', 'maxMachineCount', 'int');
        $this->addColumn('software', 'isMobileOnly', 'boolean not null default true');
        $this->addColumn('software', 'split', 'float');
        $this->addColumn('software', 'installDate', 'date');
        $this->addColumn('software', 'notes', 'text');
        
        $this->renameColumn('location', 'mobileSoftware', 'software');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230303_095037_extend_mobile_software cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230303_095037_extend_mobile_software cannot be reverted.\n";

        return false;
    }
    */
}
