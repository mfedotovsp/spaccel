<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133515_add_column_deleted_at_for_table_mvps
 */
class m230423_133515_add_column_deleted_at_for_table_mvps extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('mvps', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
