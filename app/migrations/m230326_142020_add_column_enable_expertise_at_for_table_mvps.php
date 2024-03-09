<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_142020_add_column_enable_expertise_at_for_table_mvps
 */
class m230326_142020_add_column_enable_expertise_at_for_table_mvps extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('mvps', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
