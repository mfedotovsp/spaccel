<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_134024_add_column_deleted_at_for_table_answers_questions_confirm_gcp
 */
class m230423_134024_add_column_deleted_at_for_table_answers_questions_confirm_gcp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('answers_questions_confirm_gcp', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
