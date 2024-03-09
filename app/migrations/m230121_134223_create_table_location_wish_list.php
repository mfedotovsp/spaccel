<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230121_134223_create_table_location_wish_list
 */
class m230121_134223_create_table_location_wish_list extends Migration
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

        $this->createTable('location_wish_list', [
            'id' => $this->primaryKey(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
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
