<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "installation".
 *
 * @property int $id
 * @property string $serial
 * @property string $email
 * @property string $createdAt
 * @property string $handledAt
 */
class Installation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'installation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['serial', 'email'], 'required'],
            [['createdAt', 'handledAt'], 'safe'],
            [['serial'], 'string', 'max' => 32],
            [['email'], 'string', 'max' => 128],
            [['serial'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'serial' => Yii::t('app', 'Serial'),
            'email' => Yii::t('app', 'Email'),
            'createdAt' => Yii::t('app', 'Created At'),
            'handledAt' => Yii::t('app', 'Handled At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return InstallationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InstallationQuery(get_called_class());
    }
}
