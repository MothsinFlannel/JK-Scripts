<?php

use yii\db\Migration;

/**
 * Class m230828_180141_add_canceled_job_status
 */
class m230828_180141_add_canceled_job_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TYPE [[jobState]] ADD VALUE \'canceled\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230828_180141_add_canceled_job_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230828_180141_add_canceled_job_status cannot be reverted.\n";

        return false;
    }
    */
}
