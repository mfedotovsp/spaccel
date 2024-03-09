<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_141502_add_column_enable_expertise_at_for_table_gcps
 */
class m230326_141502_add_column_enable_expertise_at_for_table_gcps extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('gcps', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
