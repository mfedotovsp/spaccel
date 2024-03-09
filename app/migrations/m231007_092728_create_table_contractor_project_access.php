<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231007_092728_create_table_contractor_project_access
 */
class m231007_092728_create_table_contractor_project_access extends Migration
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

        $this->createTable('contractor_project_access', [
            'id' => $this->primaryKey(11)->unsigned(),
            'contractor_id' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'communication_id' => $this->integer(11)->notNull(),
            'communication_type' => $this->integer(11)->notNull(),
            'stage' => $this->integer(11),
            'stage_id' => $this->integer(11),
            'date_stop' => $this->integer(11),
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
