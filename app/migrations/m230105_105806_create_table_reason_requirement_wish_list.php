<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230105_105806_create_table_reason_requirement_wish_list
 */
class m230105_105806_create_table_reason_requirement_wish_list extends Migration
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

        $this->createTable('reason_requirement_wish_list', [
            'id' => $this->primaryKey(11)->unsigned(),
            'requirement_wish_list_id' => $this->integer(11)->notNull(),
            'reason' => $this->text(),
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
