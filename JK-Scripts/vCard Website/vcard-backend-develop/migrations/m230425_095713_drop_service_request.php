<?php

use yii\db\Migration;

/**
 * Class m230425_095713_drop_service_request
 */
class m230425_095713_drop_service_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('location', 'serviceRequest');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230425_095713_drop_service_request cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230425_095713_drop_service_request cannot be reverted.\n";

        return false;
    }
    */
}
