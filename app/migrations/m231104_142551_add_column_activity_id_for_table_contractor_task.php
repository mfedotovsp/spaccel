<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231104_142551_add_column_activity_id_for_table_contractor_task
 */
class m231104_142551_add_column_activity_id_for_table_contractor_task extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('contractor_tasks', 'activity_id', $this->integer(11)->notNull());
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
