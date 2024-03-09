<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_150859_create_table_conversation_manager
 */
class m221225_150859_create_table_conversation_manager extends Migration
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

        $this->createTable('conversation_manager', [
            'id' => $this->primaryKey(11)->unsigned(),
            'manager_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'role' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)
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
