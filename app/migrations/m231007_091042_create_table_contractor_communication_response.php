<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231007_091042_create_table_contractor_communication_response
 */
class m231007_091042_create_table_contractor_communication_response extends Migration
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

        $this->createTable('contractor_communication_response', [
            'id' => $this->primaryKey(11)->unsigned(),
            'communication_id' => $this->integer(11)->notNull(),
            'answer' => $this->integer(11)->notNull(),
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
