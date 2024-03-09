<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_123826_create_table_types_access_to_expertise
 */
class m230104_123826_create_table_types_access_to_expertise extends Migration
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

        $this->createTable('types_access_to_expertise', [
            'id' => $this->primaryKey(11)->unsigned(),
            'communication_id' => $this->integer(11)->notNull(),
            'project_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'types' => $this->string(255)->notNull(),
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
