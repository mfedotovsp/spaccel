<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230916_094501_create_table_contractor_users
 */
class m230916_094501_create_table_contractor_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('contractor_users', [
            'contractor_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('index_contractor_users_contractor_id', 'contractor_users', 'contractor_id');
        $this->createIndex('index_contractor_users_user_id', 'contractor_users', 'user_id');
        $this->createIndex('index_contractor_users_unique', 'contractor_users', 'contractor_id, user_id', true);
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
