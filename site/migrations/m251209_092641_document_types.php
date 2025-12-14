<?php

use yii\db\Migration;

class m251209_092641_document_types extends Migration
{
    const TABLE_NAME = 'document_types';
    private $types = [
        'unknown',
        'invoice',
        'bankStatement',
        'payroll',
        'contract',
        'taxReturn',
        'other',

    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('document_types', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->unique(),
            'create_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'update_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        foreach ($this->types as $type) {
            $this->insert(self::TABLE_NAME, [
                'name' => $type,
            ]);
        }

        $this->addColumn('documents', 'type_id', $this->integer()->defaultValue(null));
        $this->addForeignKey(
            'fk_documents_type_id',
            'documents',
            'type_id',
            'document_types',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_documents_type_id', 'documents');
        $this->dropColumn('documents', 'type_id');
        $this->dropTable(self::TABLE_NAME);
    }
}
