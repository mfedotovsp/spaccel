<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_152305_create_table_customer_tracker
 */
class m221225_152305_create_table_customer_tracker extends Migration
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

        $this->createTable('customer_tracker', [
            'id' => $this->primaryKey(11)->unsigned(),
            'user_id' => $this->integer(11)->notNull(),
            'client_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull()
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
