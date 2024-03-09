<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230319_161705_add_column_hypothesis_id_for_table_project_communications
 */
class m230319_161705_add_column_hypothesis_id_for_table_project_communications extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('project_communications', 'hypothesis_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
