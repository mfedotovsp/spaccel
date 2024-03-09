<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230812_081230_create_table_client_codes
 */
class m230812_081230_create_table_client_codes extends Migration
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

        $this->createTable('client_codes', [
            'id' => $this->primaryKey(11)->unsigned(),
            'client_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'code' => $this->string(255)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
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
