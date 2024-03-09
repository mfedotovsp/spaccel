<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133817_add_column_deleted_at_for_table_interview_confirm_problem
 */
class m230423_133817_add_column_deleted_at_for_table_interview_confirm_problem extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('interview_confirm_problem', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
