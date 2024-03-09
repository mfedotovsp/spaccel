<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_152846_create_table_expected_results_interview_confirm_problem
 */
class m221225_152846_create_table_expected_results_interview_confirm_problem extends Migration
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

        $this->createTable('expected_results_interview_confirm_problem', [
            'id' => $this->primaryKey(11)->unsigned(),
            'problem_id' => $this->integer(11)->notNull(),
            'question' => $this->string(255)->notNull(),
            'answer' => $this->string(255)->notNull(),
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
