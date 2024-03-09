<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231122_151928_add_column_contractor_id_for_table_mvps
 */
class m231122_151928_add_column_contractor_id_for_table_mvps extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('mvps', 'contractor_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
