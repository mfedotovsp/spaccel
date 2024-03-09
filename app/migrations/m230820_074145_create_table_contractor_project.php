<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230820_074145_create_table_contractor_project
 */
class m230820_074145_create_table_contractor_project extends Migration
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

        $this->createTable('contractor_project', [
            'contractor_id' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'activity_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'deleted_at' => $this->integer(11),
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
