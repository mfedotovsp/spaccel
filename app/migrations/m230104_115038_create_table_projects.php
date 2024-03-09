<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_115038_create_table_projects
 */
class m230104_115038_create_table_projects extends Migration
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

        $this->createTable('projects', [
            'id' => $this->primaryKey(11)->unsigned(),
            'user_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
            'project_fullname' => $this->string(255)->notNull(),
            'project_name' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'purpose_project' => $this->text()->notNull(),
            'rid' => $this->string(255),
            'patent_number' => $this->string(255),
            'patent_date' => $this->integer(11),
            'patent_name' => $this->string(255),
            'core_rid' => $this->text(),
            'technology' => $this->string(255),
            'layout_technology' => $this->text(),
            'register_name' => $this->string(255),
            'register_date' => $this->integer(11),
            'site' => $this->string(255),
            'invest_name' => $this->string(255),
            'invest_date' => $this->integer(11),
            'invest_amount' => $this->integer(11),
            'date_of_announcement' => $this->integer(11),
            'announcement_event' => $this->string(255),
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
