<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_081625_create_table_expert_info
 */
class m230104_081625_create_table_expert_info extends Migration
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

        $this->createTable('expert_info', [
            'id' => $this->primaryKey(11)->unsigned(),
            'user_id' => $this->integer(11)->notNull(),
            'education' => $this->string(255)->notNull(),
            'academic_degree' => $this->string(255)->notNull(),
            'position' => $this->string(255)->notNull(),
            'type' => $this->string(255)->notNull(),
            'scope_professional_competence' => $this->text()->notNull(),
            'publications' => $this->text()->notNull(),
            'implemented_projects' => $this->text()->notNull(),
            'role_in_implemented_projects' => $this->text()->notNull(),
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
