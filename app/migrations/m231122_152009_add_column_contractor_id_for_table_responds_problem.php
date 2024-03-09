<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231122_152009_add_column_contractor_id_for_table_responds_problem
 */
class m231122_152009_add_column_contractor_id_for_table_responds_problem extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('responds_problem', 'contractor_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
