<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_135555_add_column_enable_expertise_at_for_table_confirm_segment
 */
class m230326_135555_add_column_enable_expertise_at_for_table_confirm_segment extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_segment', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
