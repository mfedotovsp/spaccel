<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230211_060220_add_column_use_wish_list_for_table_segments
 */
class m230211_060220_add_column_use_wish_list_for_table_segments extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('segments', 'use_wish_list', "ENUM('0', '1') NOT NULL DEFAULT '0'");
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
