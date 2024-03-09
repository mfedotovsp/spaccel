<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231125_124222_add_column_task_id_for_table_problems
 */
class m231125_124222_add_column_task_id_for_table_problems extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('problems', 'task_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
