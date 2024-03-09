<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_143020_create_table_client_settings
 */
class m221225_143020_create_table_client_settings extends Migration
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

        $this->createTable('client_settings', [
            'id' => $this->primaryKey(11)->unsigned(),
            'client_id' => $this->integer(11)->notNull(),
            'admin_id' => $this->integer(11)->notNull(),
            'avatar_max_image' => $this->string(255),
            'avatar_image' => $this->string(255),
            'access_admin' => $this->integer(11)->notNull()
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
