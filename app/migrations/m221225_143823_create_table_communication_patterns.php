<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_143823_create_table_communication_patterns
 */
class m221225_143823_create_table_communication_patterns extends Migration
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

        $this->createTable('communication_patterns', [
            'id' => $this->primaryKey(11)->unsigned(),
            'communication_type' => $this->integer(11)->notNull(),
            'initiator' => $this->integer(11)->notNull(),
            'is_active' => $this->integer(11)->notNull(),
            'description' => $this->string(255)->notNull(),
            'project_access_period' => $this->integer(11),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
            'is_remote' => $this->integer(11)->notNull(),
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
