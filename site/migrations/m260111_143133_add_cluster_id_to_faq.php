<?php

use yii\db\Migration;

class m260111_143133_add_cluster_id_to_faq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('faq', 'cluster_id', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('faq', 'cluster_id');
    }
}
