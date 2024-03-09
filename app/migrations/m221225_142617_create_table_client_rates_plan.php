<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_142617_create_table_client_rates_plan
 */
class m221225_142617_create_table_client_rates_plan extends Migration
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

        $this->createTable('client_rates_plan', [
            'id' => $this->primaryKey(11)->unsigned(),
            'client_id' => $this->integer(11)->notNull(),
            'rates_plan_id' => $this->integer(11)->notNull(),
            'date_start' => $this->integer(11)->notNull(),
            'date_end' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull()
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
