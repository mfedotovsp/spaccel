<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133124_add_column_deleted_at_for_table_projects
 */
class m230423_133124_add_column_deleted_at_for_table_projects extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('projects', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
