<?php

use app\models\DocumentType;
use yii\db\Migration;

class m260122_114106_adding_hames_of_document_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DocumentType::tableName(), 'name_ru', $this->string(32));
        $this->addColumn(DocumentType::tableName(), 'name_rs', $this->string(32));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DocumentType::tableName(), 'name_ru');
        $this->dropColumn(DocumentType::tableName(), 'name_rs');
    }
}
