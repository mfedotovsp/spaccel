<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230326_142419_add_column_enable_expertise_at_for_table_business_model
 */
class m230326_142419_add_column_enable_expertise_at_for_table_business_model extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('business_model', 'enable_expertise_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
