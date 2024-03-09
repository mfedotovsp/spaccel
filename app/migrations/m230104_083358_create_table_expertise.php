<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_083358_create_table_expertise
 */
class m230104_083358_create_table_expertise extends Migration
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

        $this->createTable('expertise', [
            'id' => $this->primaryKey(11)->unsigned(),
            'stage' => $this->integer(11)->notNull(),
            'stage_id' => $this->integer(11)->notNull(),
            'expert_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'type_expert' => $this->integer(11)->notNull(),
            'estimation' => $this->string(255)->notNull(),
            'comment' => $this->text()->notNull(),
            'communication_id' => $this->integer(11)->notNull(),
            'completed' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
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
