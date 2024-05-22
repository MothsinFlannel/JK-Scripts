<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attachment".
 *
 * @property int $id
 * @property string $referenceType
 * @property int $referenceId
 * @property string $title
 * @property string $file
 * @property string $type
 * @property string $createdAt
 */
class Attachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['referenceType', 'referenceId'], 'required'],
            [['referenceId'], 'default', 'value' => null],
            [['referenceId'], 'integer'],
            [['createdAt'], 'safe'],
            [['referenceType'], 'string', 'max' => 64],
            [['title', 'file'], 'string', 'max' => 256],
            [['type'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'referenceType' => Yii::t('app', 'Reference Type'),
            'referenceId' => Yii::t('app', 'Reference ID'),
            'title' => Yii::t('app', 'Title'),
            'file' => Yii::t('app', 'File'),
            'type' => Yii::t('app', 'Type'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return AttachmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AttachmentQuery(get_called_class());
    }
}
