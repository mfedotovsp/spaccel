<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m240211_095427_create_table_contractor_task_products
 */
class m240211_095427_create_table_contractor_task_products extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('contractor_task_products', [
            'id' => $this->primaryKey(11)->unsigned(),
            'contractor_id' => $this->integer(11)->notNull(),
            'task_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'price' => $this->integer(11)->notNull(),
            'satisfaction' => $this->integer(11)->notNull(),
            'flaws' => $this->string(500)->notNull(),
            'advantages' => $this->string(500)->notNull(),
            'suppliers' => $this->string(500)->notNull(),
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
