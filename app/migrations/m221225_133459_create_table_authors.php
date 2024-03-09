<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_133459_create_table_authors
 */
class m221225_133459_create_table_authors extends Migration
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

        $this->createTable('authors', [
            'id' => $this->primaryKey(11)->unsigned(),
            'project_id' => $this->integer(11)->notNull(),
            'fio' => $this->string(255)->notNull(),
            'role' => $this->string(255)->notNull(),
            'experience' => $this->text()
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
