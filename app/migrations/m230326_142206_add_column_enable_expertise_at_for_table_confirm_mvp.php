<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_142206_add_column_enable_expertise_at_for_table_confirm_mvp
 */
class m230326_142206_add_column_enable_expertise_at_for_table_confirm_mvp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_mvp', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
