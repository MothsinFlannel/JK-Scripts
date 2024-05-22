<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $authorId
 * @property int $boardId
 * @property string $column
 * @property string $summary
 * @property string $description
 *
 * @property Board $board
 * @property User $author
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['authorId', 'boardId'], 'required'],
            [['authorId', 'boardId'], 'default', 'value' => null],
            [['authorId', 'boardId'], 'integer'],
            [['description'], 'string'],
            [['column'], 'string', 'max' => 64],
            [['summary'], 'string', 'max' => 256],
            [['boardId'], 'exist', 'skipOnError' => true, 'targetClass' => Board::className(), 'targetAttribute' => ['boardId' => 'id']],
            [['authorId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['authorId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'authorId' => Yii::t('app', 'Author ID'),
            'boardId' => Yii::t('app', 'Board ID'),
            'column' => Yii::t('app', 'Column'),
            'summary' => Yii::t('app', 'Summary'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoard()
    {
        return $this->hasOne(Board::className(), ['id' => 'boardId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'authorId']);
    }

    /**
     * {@inheritdoc}
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }
}
