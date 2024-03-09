<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133200_add_column_deleted_at_for_table_segments
 */
class m230423_133200_add_column_deleted_at_for_table_segments extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('segments', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
