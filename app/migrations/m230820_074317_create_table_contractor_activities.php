<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230820_074317_create_table_contractor_activities
 */
class m230820_074317_create_table_contractor_activities extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('contractor_activities', [
            'id' => $this->primaryKey(11)->unsigned(),
            'title' => $this->string(255)->notNull(),
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
