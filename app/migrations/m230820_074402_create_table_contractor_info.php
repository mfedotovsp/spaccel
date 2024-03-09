<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230820_074402_create_table_contractor_info
 */
class m230820_074402_create_table_contractor_info extends Migration
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

        $this->createTable('contractor_info', [
            'id' => $this->primaryKey(11)->unsigned(),
            'contractor_id' => $this->integer(11)->notNull(),
            'activities' => $this->string(255)->notNull(),
            'academic_degree' => $this->string(255),
            'position' => $this->string(255),
            'publications' => $this->text(),
            'implemented_projects' => $this->text(),
            'role_in_implemented_projects' => $this->string(255),
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
