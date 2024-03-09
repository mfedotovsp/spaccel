<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_110906_create_table_pre_files
 */
class m230104_110906_create_table_pre_files extends Migration
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

        $this->createTable('pre_files', [
            'id' => $this->primaryKey(11)->unsigned(),
            'project_id' => $this->integer(11)->notNull(),
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
