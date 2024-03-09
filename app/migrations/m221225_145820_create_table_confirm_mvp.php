<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_145820_create_table_confirm_mvp
 */
class m221225_145820_create_table_confirm_mvp extends Migration
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

        $this->createTable('confirm_mvp', [
            'id' => $this->primaryKey(11)->unsigned(),
            'mvp_id' => $this->integer(11)->notNull(),
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
