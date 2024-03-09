<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_134022_create_table_business_model
 */
class m221225_134022_create_table_business_model extends Migration
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

        $this->createTable('business_model', [
            'id' => $this->primaryKey(11)->unsigned(),
            'basic_confirm_id' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'segment_id' => $this->integer(11)->notNull(),
            'problem_id' => $this->integer(11)->notNull(),
            'gcp_id' => $this->integer(11)->notNull(),
            'mvp_id' => $this->integer(11)->notNull(),
            'relations' => $this->string(255)->notNull(),
            'partners' => $this->text()->notNull(),
            'distribution_of_sales' => $this->string(255)->notNull(),
            'resources' => $this->string(255)->notNull(),
            'cost' => $this->text()->notNull(),
            'revenue' => $this->text()->notNull(),
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
