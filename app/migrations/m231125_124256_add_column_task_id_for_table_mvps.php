<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231125_124256_add_column_task_id_for_table_mvps
 */
class m231125_124256_add_column_task_id_for_table_mvps extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('mvps', 'task_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
