<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231125_124324_add_column_task_id_for_table_responds_segment
 */
class m231125_124324_add_column_task_id_for_table_responds_segment extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('responds_segment', 'task_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
