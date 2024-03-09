<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_094543_create_table_interview_confirm_segment
 */
class m230104_094543_create_table_interview_confirm_segment extends Migration
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

        $this->createTable('interview_confirm_segment', [
            'id' => $this->primaryKey(11)->unsigned(),
            'respond_id' => $this->integer(11)->notNull(),
            'interview_file' => $this->string(255),
            'server_file' => $this->string(255),
            'result' => $this->text()->notNull(),
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
