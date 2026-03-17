<?php

use app\models\Company;
use yii\db\Migration;

class m260317_095122_minimax_fields_for_companies extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Company::tableName(), 'minimax_id', $this->bigInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Company::tableName(), 'minimax_id');
    }

}
