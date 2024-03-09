<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230430_084812_add_column_deleted_at_for_table_authors
 */
class m230430_084812_add_column_deleted_at_for_table_authors extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('authors', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
