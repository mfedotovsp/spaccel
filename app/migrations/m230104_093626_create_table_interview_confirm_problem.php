<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_093626_create_table_interview_confirm_problem
 */
class m230104_093626_create_table_interview_confirm_problem extends Migration
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

        $this->createTable('interview_confirm_problem', [
            'id' => $this->primaryKey(11)->unsigned(),
            'respond_id' => $this->integer(11)->notNull(),
            'interview_file' => $this->string(255),
            'server_file' => $this->string(255),
            'status' => "ENUM('0', '1') NOT NULL DEFAULT '0'",
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
