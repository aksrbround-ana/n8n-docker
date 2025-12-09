<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_types".
 *
 * @property int $id
 * @property string $name
 * @property string $create_at
 * @property string $update_at
 *
 * @property Documents[] $documents
 */
class DocumentType extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['create_at', 'update_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }

    /**
     * Gets query for [[Documents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Document::class, ['type_id' => 'id']);
    }

}
