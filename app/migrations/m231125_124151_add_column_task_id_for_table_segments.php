<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231125_124151_add_column_task_id_for_table_segments
 */
class m231125_124151_add_column_task_id_for_table_segments extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('segments', 'task_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
