<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_145025_create_table_communication_response
 */
class m221225_145025_create_table_communication_response extends Migration
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

        $this->createTable('communication_response', [
            'id' => $this->primaryKey(11)->unsigned(),
            'communication_id' => $this->integer(11)->notNull(),
            'answer' => $this->integer(11)->notNull(),
            'expert_types' => $this->string(255),
            'comment' => $this->string(255),
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
