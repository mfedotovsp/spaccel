<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230423_133620_add_column_deleted_at_for_table_business_model
 */
class m230423_133620_add_column_deleted_at_for_table_business_model extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('business_model', 'deleted_at', $this->integer(11));
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
