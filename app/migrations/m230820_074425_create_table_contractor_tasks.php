<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230820_074425_create_table_contractor_tasks
 */
class m230820_074425_create_table_contractor_tasks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('contractor_tasks', [
            'id' => $this->primaryKey(11)->unsigned(),
            'contractor_id' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->notNull(),
            'hypothesis_id' => $this->integer(11)->notNull(),
            'description' => $this->text()->notNull(),
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
