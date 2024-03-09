<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_121328_create_table_rates_plan
 */
class m230104_121328_create_table_rates_plan extends Migration
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

        $this->createTable('rates_plan', [
            'id' => $this->primaryKey(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'max_count_project_user' => $this->integer(11)->notNull(),
            'max_count_tracker' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
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
