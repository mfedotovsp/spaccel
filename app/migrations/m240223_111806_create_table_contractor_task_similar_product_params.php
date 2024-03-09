<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m240223_111806_create_table_contractor_task_similar_product_params
 */
class m240223_111806_create_table_contractor_task_similar_product_params extends Migration
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

        $this->createTable('contractor_task_similar_product_params', [
            'id' => $this->primaryKey(11)->unsigned(),
            'task_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
            'deleted_at' => $this->integer(11),
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
