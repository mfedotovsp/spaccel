<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240819174315CreateTableConfirmFiles
 */
class M240819174315CreateTableConfirmFiles extends Migration
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

        $this->createTable('confirm_files', [
            'id' => $this->primaryKey(11)->unsigned(),
            'source_id' => $this->integer(11)->notNull(),
            'file_name' => $this->string(255)->notNull(),
            'server_file' => $this->string(255)->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->delete('confirm_files');
    }
}
