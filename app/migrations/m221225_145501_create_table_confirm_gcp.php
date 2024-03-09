<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_145501_create_table_confirm_gcp
 */
class m221225_145501_create_table_confirm_gcp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('confirm_gcp', [
            'id' => $this->primaryKey(11)->unsigned(),
            'gcp_id' => $this->integer(11)->notNull(),
            'count_respond' => $this->integer(11)->notNull(),
            'count_positive' => $this->integer(11)->notNull(),
            'enable_expertise' => "ENUM('0', '1') NOT NULL DEFAULT '0'"
        ], $tableOptions);
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
