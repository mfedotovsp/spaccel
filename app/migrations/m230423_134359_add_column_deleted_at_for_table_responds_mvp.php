<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_134359_add_column_deleted_at_for_table_responds_mvp
 */
class m230423_134359_add_column_deleted_at_for_table_responds_mvp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('responds_mvp', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
