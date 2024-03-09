<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_142235_create_table_client_activation
 */
class m221225_142235_create_table_client_activation extends Migration
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

        $this->createTable('client_activation', [
            'id' => $this->primaryKey(11)->unsigned(),
            'client_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->notNull(),
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
