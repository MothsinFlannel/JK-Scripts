<?php

use yii\db\Migration;

/**
 * Class m201022_203916_replace_convolution_unique
 */
class m201022_203916_replace_convolution_unique extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('unique', 'convolution');
        $this->createIndex('convolutionUnique', 'convolution', ['locationId', 'date', 'terminal', 'isLive'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_203916_replace_convolution_unique cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_203916_replace_convolution_unique cannot be reverted.\n";

        return false;
    }
    */
}
