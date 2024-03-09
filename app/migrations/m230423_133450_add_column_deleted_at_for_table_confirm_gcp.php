<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133450_add_column_deleted_at_for_table_confirm_gcp
 */
class m230423_133450_add_column_deleted_at_for_table_confirm_gcp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_gcp', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
