<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231122_151851_add_column_contractor_id_for_table_problems
 */
class m231122_151851_add_column_contractor_id_for_table_problems extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('problems', 'contractor_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
