<?php

use app\models\Settings;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m260311_095728_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Settings::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string(20)->notNull(),
            'value' => $this->string(100),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Settings::tableName());
    }
}
