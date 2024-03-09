<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230430_092727_add_column_deleted_at_for_table_segment_requirement
 */
class m230430_092727_add_column_deleted_at_for_table_segment_requirement extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('segment_requirement', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
