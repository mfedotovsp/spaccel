<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m240223_111717_create_table_contractor_task_similar_products
 */
class m240223_111717_create_table_contractor_task_similar_products extends Migration
{
    /**
     * {@inheritDoc}
     * @throws \yii\base\Exception
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('contractor_task_similar_products', [
            'id' => $this->primaryKey(11)->unsigned(),
            'contractor_id' => $this->integer(11)->notNull(),
            'task_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'ownership_cost' => $this->integer(11)->notNull(),
            'price' => $this->integer(11)->notNull(),
            'params' => $this->json(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
