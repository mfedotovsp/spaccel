<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_140356_add_column_enable_expertise_at_for_table_problems
 */
class m230326_140356_add_column_enable_expertise_at_for_table_problems extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('problems', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
