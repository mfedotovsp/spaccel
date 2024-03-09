<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230430_084852_add_column_deleted_at_for_table_pre_files
 */
class m230430_084852_add_column_deleted_at_for_table_pre_files extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('pre_files', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
