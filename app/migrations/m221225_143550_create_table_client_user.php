<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_143550_create_table_client_user
 */
class m221225_143550_create_table_client_user extends Migration
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

        $this->createTable('client_user', [
            'id' => $this->primaryKey(11)->unsigned(),
            'user_id' => $this->integer(11)->notNull(),
            'client_id' => $this->integer(11)->notNull(),
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
