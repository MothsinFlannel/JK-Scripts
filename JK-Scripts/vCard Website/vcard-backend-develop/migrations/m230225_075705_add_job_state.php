<?php

use yii\db\Migration;

/**
 * Class m230225_075705_add_job_state
 */
class m230225_075705_add_job_state extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('drop type if exists "jobState"');
        $this->execute('create type "jobState" as enum(\'pending\', \'in-progress\', \'succeeded\', \'failed\')');

        $this->delete('job');
        $this->addColumn('job', 'state', '"jobState" not null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230225_075705_add_job_state cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230225_075705_add_job_state cannot be reverted.\n";

        return false;
    }
    */
}
