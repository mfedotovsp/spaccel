<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_122343_create_table_all_questions_confirm_problem
 */
class m221225_122343_create_table_all_questions_confirm_problem extends Migration
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

        $this->createTable('all_questions_confirm_problem', [
            'id' => $this->primaryKey(11)->unsigned(),
            'title' => $this->string(255)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull()
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
