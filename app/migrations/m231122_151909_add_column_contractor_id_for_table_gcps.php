<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231122_151909_add_column_contractor_id_for_table_gcps
 */
class m231122_151909_add_column_contractor_id_for_table_gcps extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('gcps', 'contractor_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
