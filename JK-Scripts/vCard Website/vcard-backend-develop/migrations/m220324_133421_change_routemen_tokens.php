<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m220324_133421_change_routemen_tokens
 */
class m220324_133421_change_routemen_tokens extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->from('user')
            ->andWhere(['role' => 'routeman'])
            ->asArray();

        foreach ($query->each() as $each) {
            $this->update('user', [
                'accessToken' => Yii::$app->security->generateRandomString()
            ], [
                'id' => $each['id']
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220324_133421_change_routemen_tokens cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220324_133421_change_routemen_tokens cannot be reverted.\n";

        return false;
    }
    */
}
