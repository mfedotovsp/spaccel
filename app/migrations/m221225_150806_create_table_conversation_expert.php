<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_150806_create_table_conversation_expert
 */
class m221225_150806_create_table_conversation_expert extends Migration
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

        $this->createTable('conversation_expert', [
            'id' => $this->primaryKey(11)->unsigned(),
            'expert_id' => $this->integer(11)->notNull(),
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
