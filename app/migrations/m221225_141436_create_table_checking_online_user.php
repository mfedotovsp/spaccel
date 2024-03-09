<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_141436_create_table_checking_online_user
 */
class m221225_141436_create_table_checking_online_user extends Migration
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

        $this->createTable('checking_online_user', [
            'id' => $this->primaryKey(11)->unsigned(),
            'user_id' => $this->integer(11)->notNull(),
            'last_active_time' => $this->integer(11)->notNull(),
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
