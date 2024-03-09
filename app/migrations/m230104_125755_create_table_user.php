<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_125755_create_table_user
 */
class m230104_125755_create_table_user extends Migration
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

        $this->createTable('user', [
            'id' => $this->primaryKey(11)->unsigned(),
            'email' => $this->string(255)->notNull(),
            'username' => $this->string(255)->unique()->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'avatar_max_image' => $this->string(255),
            'avatar_image' => $this->string(255),
            'auth_key' => $this->string(255),
            'secret_key' => $this->string(255),
            'role' => $this->integer(11)->notNull(),
            'status' => $this->smallInteger(6)->notNull(),
            'confirm' => $this->integer(11)->notNull(),
            'id_admin' => $this->integer(11),
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
