<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m201112_203753_update_invoice_fee
 */
class m201112_203753_update_invoice_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->from('invoice')
            ->asArray();

        foreach ($query->each() as $invoice) {
            $convolutionQuery = ActiveRecord::find()
                ->from('convolution')
                ->andWhere(':since <= date and date <= :until', [
                    ':since' => $invoice['since'],
                    ':until' => $invoice['until'],
                ])
                ->andWhere(['locationId' => $invoice['locationId']]);

            $revenue = $convolutionQuery->sum('[[moneyIn]] - [[moneyOut]]');
            $profit  = $convolutionQuery->sum('[[percentageProfit]] + [[flatFee]]');

            if ($revenue) {
                $calculated = round($profit / $revenue * 100, 2);
                $this->update('invoice', ['splitPercent' => $calculated], ['id' => $invoice['id']]);
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201112_203753_update_invoice_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_203753_update_invoice_fee cannot be reverted.\n";

        return false;
    }
    */
}
