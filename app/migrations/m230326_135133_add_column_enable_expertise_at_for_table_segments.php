<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_135133_add_column_enable_expertise_at_for_table_segments
 */
class m230326_135133_add_column_enable_expertise_at_for_table_segments extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('segments', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
