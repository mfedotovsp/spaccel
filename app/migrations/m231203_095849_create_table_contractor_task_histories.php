<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231203_095849_create_table_contractor_task_histories
 */
class m231203_095849_create_table_contractor_task_histories extends Migration
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

        $this->createTable('contractor_task_histories', [
            'id' => $this->primaryKey(11)->unsigned(),
            'task_id' => $this->integer(11)->notNull(),
            'old_status' => $this->integer(11)->notNull(),
            'new_status' => $this->integer(11)->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->integer(11)->notNull(),
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
