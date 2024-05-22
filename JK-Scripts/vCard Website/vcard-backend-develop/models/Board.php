<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "board".
 *
 * @property int $id
 * @property string $title
 * @property string $externalId
 * @property array $columns
 * @property bool $isActive
 * @property string $createdAt
 *
 * @property Task[] $tasks
 */
class Board extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'board';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['isActive'], 'boolean'],
            [['createdAt'], 'safe'],
            [['title'], 'string', 'max' => 256],
            [['externalId'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'externalId' => Yii::t('app', 'External ID'),
            'columns' => Yii::t('app', 'Columns'),
            'isActive' => Yii::t('app', 'Is Active'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['boardId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return BoardQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BoardQuery(get_called_class());
    }
}
