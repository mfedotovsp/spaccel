<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133353_add_column_deleted_at_for_table_problems
 */
class m230423_133353_add_column_deleted_at_for_table_problems extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('problems', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
