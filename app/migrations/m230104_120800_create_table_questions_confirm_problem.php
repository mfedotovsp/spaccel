<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_120800_create_table_questions_confirm_problem
 */
class m230104_120800_create_table_questions_confirm_problem extends Migration
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

        $this->createTable('questions_confirm_problem', [
            'id' => $this->primaryKey(11)->unsigned(),
            'confirm_id' => $this->integer(11)->notNull(),
            'title' => $this->string(255),
            'status' => $this->integer(11)->notNull(),
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
