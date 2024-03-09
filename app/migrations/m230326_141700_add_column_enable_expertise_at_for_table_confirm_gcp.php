<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_141700_add_column_enable_expertise_at_for_table_confirm_gcp
 */
class m230326_141700_add_column_enable_expertise_at_for_table_confirm_gcp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_gcp', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
