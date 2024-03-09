<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231122_151957_add_column_contractor_id_for_table_responds_segment
 */
class m231122_151957_add_column_contractor_id_for_table_responds_segment extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('responds_segment', 'contractor_id', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
