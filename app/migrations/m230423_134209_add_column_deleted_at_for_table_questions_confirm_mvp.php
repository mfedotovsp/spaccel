<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_134209_add_column_deleted_at_for_table_questions_confirm_mvp
 */
class m230423_134209_add_column_deleted_at_for_table_questions_confirm_mvp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('questions_confirm_mvp', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
