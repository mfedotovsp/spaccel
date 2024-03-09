<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_104711_create_table_mvps
 */
class m230104_104711_create_table_mvps extends Migration
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

        $this->createTable('mvps', [
            'id' => $this->primaryKey(11)->unsigned(),
            'basic_confirm_id' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'segment_id' => $this->integer(11)->notNull(),
            'problem_id' => $this->integer(11)->notNull(),
            'gcp_id' => $this->integer(11)->notNull(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'time_confirm' => $this->integer(11),
            'exist_confirm' => $this->integer(11),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
            'enable_expertise' => "ENUM('0', '1') NOT NULL DEFAULT '0'"
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
