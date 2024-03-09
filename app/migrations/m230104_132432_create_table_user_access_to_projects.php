<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_132432_create_table_user_access_to_projects
 */
class m230104_132432_create_table_user_access_to_projects extends Migration
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

        $this->createTable('user_access_to_projects', [
            'id' => $this->primaryKey(11)->unsigned(),
            'user_id' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'communication_id' => $this->integer(11)->notNull(),
            'communication_type' => $this->integer(11)->notNull(),
            'cancel' => $this->integer(11)->notNull(),
            'date_stop' => $this->integer(11),
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
