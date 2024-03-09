<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231007_085754_create_table_contractor_communications
 */
class m231007_085754_create_table_contractor_communications extends Migration
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

        $this->createTable('contractor_communications', [
            'id' => $this->primaryKey(11)->unsigned(),
            'sender_id' => $this->integer(11)->notNull(),
            'adressee_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'activity_id' => $this->integer(11)->notNull(),
            'stage' => $this->integer(11),
            'stage_id' => $this->integer(11),
            'status' => $this->integer(11)->notNull(),
            'triggered_communication_id' => $this->integer(11),
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
