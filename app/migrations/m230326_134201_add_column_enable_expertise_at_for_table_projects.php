<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_134201_add_column_enable_expertise_at_for_table_projects
 */
class m230326_134201_add_column_enable_expertise_at_for_table_projects extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('projects', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
