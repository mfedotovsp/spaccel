<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_112114_create_table_project_communications
 */
class m230104_112114_create_table_project_communications extends Migration
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

        $this->createTable('project_communications', [
            'id' => $this->primaryKey(11)->unsigned(),
            'sender_id' => $this->integer(11)->notNull(),
            'adressee_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->notNull(),
            'pattern_id' => $this->integer(11),
            'triggered_communication_id' => $this->integer(11),
            'cancel' => $this->integer(11)->notNull(),
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
