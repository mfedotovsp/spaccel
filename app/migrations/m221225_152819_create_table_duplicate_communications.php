<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_152819_create_table_duplicate_communications
 */
class m221225_152819_create_table_duplicate_communications extends Migration
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

        $this->createTable('duplicate_communications', [
            'id' => $this->primaryKey(11)->unsigned(),
            'type' => $this->integer(11)->notNull(),
            'source_id' => $this->integer(11)->notNull(),
            'sender_id' => $this->integer(11)->notNull(),
            'adressee_id' => $this->integer(11)->notNull(),
            'description' => $this->text()->notNull(),
            'status' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull()
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
