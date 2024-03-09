<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_145838_create_table_confirm_problem
 */
class m221225_145838_create_table_confirm_problem extends Migration
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

        $this->createTable('confirm_problem', [
            'id' => $this->primaryKey(11)->unsigned(),
            'problem_id' => $this->integer(11)->notNull(),
            'count_respond' => $this->integer(11)->notNull(),
            'count_positive' => $this->integer(11)->notNull(),
            'need_consumer' => $this->string(255)->notNull(),
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
