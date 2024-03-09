<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_150746_create_table_conversation_development
 */
class m221225_150746_create_table_conversation_development extends Migration
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

        $this->createTable('conversation_development', [
            'id' => $this->primaryKey(11)->unsigned(),
            'dev_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
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
