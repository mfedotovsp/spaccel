<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_150825_create_table_conversation_main_admin
 */
class m221225_150825_create_table_conversation_main_admin extends Migration
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

        $this->createTable('conversation_main_admin', [
            'id' => $this->primaryKey(11)->unsigned(),
            'main_admin_id' => $this->integer(11)->notNull(),
            'admin_id' => $this->integer(11)->notNull(),
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
