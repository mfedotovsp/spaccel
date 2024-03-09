<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m240226_170807_create_table_contractor_task_files
 */
class m240226_170807_create_table_contractor_task_files extends Migration
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

        $this->createTable('contractor_task_files', [
            'id' => $this->primaryKey(11)->unsigned(),
            'task_id' => $this->integer(11)->notNull(),
            'file_name' => $this->string(255)->notNull(),
            'server_file' => $this->string(255)->notNull(),
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
