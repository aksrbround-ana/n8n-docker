<?php

use yii\db\Migration;

class m251120_124905_create_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company_type}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(512),
        ]);
        $this->insert('{{%company_type}}', ['name' => 'DOO']);
        $this->insert('{{%company_type}}', ['name' => 'Knigaš']);
        $this->insert('{{%company_type}}', ['name' => 'Paušal']);

        $this->createTable('{{%company_activities}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(32),
        ]);
        $this->insert('{{%company_activities}}', ['name' => 'trgovina']);
        $this->insert('{{%company_activities}}', ['name' => 'programiranje']);
        $this->insert('{{%company_activities}}', ['name' => 'reklama']);
        $this->insert('{{%company_activities}}', ['name' => 'kafić']);
        $this->insert('{{%company_activities}}', ['name' => 'sushi']);
        $this->insert('{{%company_activities}}', ['name' => 'konsalting']);
        $this->insert('{{%company_activities}}', ['name' => 'prozori']);
        $this->insert('{{%company_activities}}', ['name' => 'posrednik']);
        $this->insert('{{%company_activities}}', ['name' => 'obrazovanje']);
        $this->insert('{{%company_activities}}', ['name' => 'turizam']);

        $this->createTable('{{%company}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(512),
            'name_tg' => $this->string(512),
            'type_id' => $this->bigInteger(),
            'is_pdv' => $this->boolean()->defaultValue(false),
            'activity_id' => $this->bigInteger(),
            'specific_reports' => $this->string(256),
            'report_date' => $this->integer(),
            'reminder' => $this->string(256),
            'pib' => $this->bigInteger(),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
        $this->createIndex(
            'idx_company_name',
            '{{%company}}',
            'name'
        );
        $this->createIndex(
            'idx_company_pib',
            '{{%company}}',
            'pib',
            true
        );
        $this->addForeignKey(
            'fk_company_type',
            '{{%company}}',
            'type_id',
            '{{%company_type}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_company_activities',
            '{{%company}}',
            'activity_id',
            '{{%company_activities}}',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%customer}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'tg_id' => $this->bigInteger(),
            'company_id' => $this->bigInteger(),
            'firstname' => $this->string(32),
            'lastname' => $this->string(32),
            'username' => $this->string(32),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
        $this->createIndex(
            'idx_customer_company_id',
            '{{%customer}}',
            'company_id'
        );
        $this->createIndex(
            'idx_customer_tg_id',
            '{{%customer}}',
            'tg_id'
        );
        $this->createIndex(
            'idx_customer_username',
            '{{%customer}}',
            'username'
        );

        $this->createTable('{{%accountant_activity}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(64),
            'due_date' => $this->integer(),
        ]);
        $this->insert('{{%accountant_activity}}', ['name' => 'knjiženje', 'due_date' => 5]);
        $this->insert('{{%accountant_activity}}', ['name' => 'obračun zarada', 'due_date' => 2]);
        $this->insert('{{%accountant_activity}}', ['name' => 'odgovor na pitanja vezanih za poslovanje firmi', 'due_date' => 5]);
        $this->insert('{{%accountant_activity}}', ['name' => 'pdv', 'due_date' => 5]);
        $this->insert('{{%accountant_activity}}', ['name' => 'plaćanje računa', 'due_date' => 2]);
        $this->insert('{{%accountant_activity}}', ['name' => 'prijava/odjava radnika (pio/croso)', 'due_date' => 5]);
        $this->insert('{{%accountant_activity}}', ['name' => 'promena podataka', 'due_date' => 6]);
        $this->insert('{{%accountant_activity}}', ['name' => 'rad na terenu', 'due_date' => 5]);

        $this->createTable('{{%accountant}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'firstname' => $this->string(32),
            'lastname' => $this->string(32),
            'username' => $this->string(32),
            'status' => $this->string(32)->defaultValue('new'),
        ]);

        $this->createTable('{{%company_accountant}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'accountant_id' => $this->bigInteger(),
        ]);
        $this->createIndex(
            'idx_company_accountant_unique',
            '{{%company_accountant}}',
            ['company_id', 'accountant_id' ],
            true
        );
        $this->addForeignKey(
            'fk_company_accountant_company',
            '{{%company_accountant}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_company_accountant_accountant',
            '{{%company_accountant}}',
            'accountant_id',
            '{{%accountant}}',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%accountant_accountant_activity}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'accountant_id' => $this->bigInteger(),
            'accountant_activity_id' => $this->bigInteger(),
        ]);
        $this->createIndex(
            'idx_accountant_activity_unique',
            '{{%accountant_accountant_activity}}',
            ['accountant_id', 'accountant_activity_id' ],
            true
        );
        $this->addForeignKey(
            'fk_accountant_activity_accountant',
            '{{%accountant_accountant_activity}}',
            'accountant_id',
            '{{%accountant}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_accountant_activity_activity',
            '{{%accountant_accountant_activity}}',
            'accountant_activity_id',
            '{{%accountant_activity}}',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%reminder_template}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'description' => $this->string(128),
            'text_ru' => $this->text(),
            'text_rs' => $this->text(),
        ]);

        $this->createTable('{{%reminder}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'template_id' => $this->bigInteger(),
            'send_date' => $this->integer(),
            'type' => $this->string(32),
            'message' => $this->string(512),
            'status' => $this->string(32)->defaultValue('new'),
        ]);

        $this->createTable('{{%event}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'topic' => $this->string(128),
            'details' => $this->string(128),
            'status' => $this->string(32)->defaultValue('new'),
        ]);

        $this->createTable('{{%task}}', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'category' => $this->string(64),
            'request' => $this->string(256),
            'due_date' => $this->integer(),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%task}}');
        $this->dropTable('{{%event}}');
        $this->dropTable('{{%reminder}}');
        $this->dropTable('{{%reminder_template}}');
        $this->dropForeignKey('fk_accountant_activity_activity', '{{%accountant_accountant_activity}}');
        $this->dropForeignKey('fk_accountant_activity_accountant', '{{%accountant_accountant_activity}}');
        $this->dropTable('{{%accountant_accountant_activity}}');
        $this->dropForeignKey('fk_company_accountant_accountant', '{{%company_accountant}}');
        $this->dropForeignKey('fk_company_accountant_company', '{{%company_accountant}}');
        $this->dropTable('{{%company_accountant}}');
        $this->dropTable('{{%accountant}}');
        $this->dropTable('{{%accountant_activity}}');
        $this->dropForeignKey('fk_company_activities', '{{%company}}');
        $this->dropForeignKey('fk_company_type', '{{%company}}');
        $this->dropIndex('idx_customer_company_id', '{{%customer}}');
        $this->dropIndex('idx_customer_username', '{{%customer}}');
        $this->dropIndex('idx_customer_tg_id', '{{%customer}}');
        $this->dropTable('{{%customer}}');
        $this->dropIndex('idx_company_pib', '{{%company}}');
        $this->dropIndex('idx_company_name', '{{%company}}');
        $this->dropTable('{{%company}}');
        $this->dropTable('{{%company_activities}}');
        $this->dropTable('{{%company_type}}');
    }
}
