<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133440_add_column_deleted_at_for_table_gcps
 */
class m230423_133440_add_column_deleted_at_for_table_gcps extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('gcps', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
