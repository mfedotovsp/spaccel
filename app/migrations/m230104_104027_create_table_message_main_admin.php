<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_104027_create_table_message_main_admin
 */
class m230104_104027_create_table_message_main_admin extends Migration
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

        $this->createTable('message_main_admin', [
            'id' => $this->primaryKey(11)->unsigned(),
            'conversation_id' => $this->integer(11)->notNull(),
            'sender_id' => $this->integer(11)->notNull(),
            'adressee_id' => $this->integer(11)->notNull(),
            'description' => $this->text(),
            'status' => $this->integer(11)->notNull(),
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
