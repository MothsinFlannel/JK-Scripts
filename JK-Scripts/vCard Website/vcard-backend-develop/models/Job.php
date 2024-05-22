<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "job".
 *
 * @property int $id
 * @property int $initiatorId
 * @property string $category
 * @property string $title
 * @property string $createdAt
 * @property string $endedAt
 * @property int $progress
 * @property string $output
 * @property string $state
 *
 * @property User $initiator
 */
class Job extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'job';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['initiatorId', 'progress'], 'default', 'value' => null],
            [['initiatorId', 'progress'], 'integer'],
            [['category', 'title', 'state'], 'required'],
            [['createdAt', 'endedAt'], 'safe'],
            [['state'], 'string'],
            [['category'], 'string', 'max' => 32],
            [['title', 'output'], 'string', 'max' => 256],
            [['initiatorId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['initiatorId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'initiatorId' => Yii::t('app', 'Initiator ID'),
            'category' => Yii::t('app', 'Category'),
            'title' => Yii::t('app', 'Title'),
            'createdAt' => Yii::t('app', 'Created At'),
            'endedAt' => Yii::t('app', 'Ended At'),
            'progress' => Yii::t('app', 'Progress'),
            'output' => Yii::t('app', 'Output'),
            'state' => Yii::t('app', 'State'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInitiator()
    {
        return $this->hasOne(User::className(), ['id' => 'initiatorId']);
    }

    /**
     * {@inheritdoc}
     * @return JobQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new JobQuery(get_called_class());
    }
}
