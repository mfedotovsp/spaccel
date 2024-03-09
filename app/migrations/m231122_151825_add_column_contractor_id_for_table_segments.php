<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231122_151825_add_column_contractor_id_for_table_segments
 */
class m231122_151825_add_column_contractor_id_for_table_segments extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('segments', 'contractor_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
