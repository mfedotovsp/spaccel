<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_141754_create_table_client
 */
class m221225_141754_create_table_client extends Migration
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

        $this->createTable('client', [
            'id' => $this->primaryKey(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'fullname' => $this->string(255)->notNull(),
            'city' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
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
