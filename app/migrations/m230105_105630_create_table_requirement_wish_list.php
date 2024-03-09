<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230105_105630_create_table_requirement_wish_list
 */
class m230105_105630_create_table_requirement_wish_list extends Migration
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

        $this->createTable('requirement_wish_list', [
            'id' => $this->primaryKey(11)->unsigned(),
            'wish_list_id' => $this->integer(11)->notNull(),
            'is_actual' => $this->integer(11)->notNull(),
            'requirement' => $this->text()->notNull(),
            'expected_result' => $this->text()->notNull(),
            'add_info' => $this->text()
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
