<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230121_112328_create_table_customer_wish_list
 */
class m230121_112328_create_table_customer_wish_list extends Migration
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

        $this->createTable('customer_wish_list', [
            'id' => $this->primaryKey(11)->unsigned(),
            'client_id' => $this->integer(11)->notNull(),
            'customer_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'deleted_at' => $this->integer(11)
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
