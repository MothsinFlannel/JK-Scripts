<?php

use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m201210_214302_update_last_update
 */
class m201210_214302_update_last_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->from('terminal')
            ->andWhere('[[lastActivityAt]] is null')
            ->asArray();

        foreach ($query->each() as $terminal) {
            $location = ActiveRecord::find()
                ->from('location')
                ->where(['id' => $terminal['locationId']])
                ->asArray()
                ->one();

            $log = ActiveRecord::find()
                ->from('log')
                ->andWhere([
                    'serial'   => $location['serial'],
                    'terminal' => $terminal['number']
                ])
                ->orderBy('createdAt desc')
                ->asArray()
                ->limit(1)
                ->one();

            if ($log && $log['createdAt'] <> $terminal['lastActivityAt']) {
                Console::output("Location #{$location['id']}, Terminal #{$terminal['number']}, {$terminal['lastActivityAt']} -> {$log['createdAt']}");

                $this->update('terminal', ['lastActivityAt' => $log['createdAt']], ['id' => $terminal['id']]);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201210_214302_update_last_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201210_214302_update_last_update cannot be reverted.\n";

        return false;
    }
    */
}
