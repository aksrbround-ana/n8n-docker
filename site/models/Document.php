<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documents".
 *
 * @property int $id
 * @property string|null $content
 * @property string|null $metadata
 * @property string|null $embedding
 * @property int|null $tg_id
 * @property int|null $company_id
 * @property string|null $filename
 * @property string|null $mimetype
 * @property string $create_at
 * @property string $update_at
 * @property string $status
 */
class Document extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'metadata', 'embedding', 'tg_id', 'company_id', 'filename', 'mimetype'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['content', 'embedding'], 'string'],
            [['metadata', 'create_at', 'update_at'], 'safe'],
            [['tg_id', 'company_id'], 'default', 'value' => null],
            [['tg_id', 'company_id'], 'integer'],
            [['filename'], 'string', 'max' => 512],
            [['mimetype'], 'string', 'max' => 64],
            [['status'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'metadata' => 'Metadata',
            'embedding' => 'Embedding',
            'tg_id' => 'Tg ID',
            'company_id' => 'Company ID',
            'filename' => 'Filename',
            'mimetype' => 'Mimetype',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'status' => 'Status',
        ];
    }

}
