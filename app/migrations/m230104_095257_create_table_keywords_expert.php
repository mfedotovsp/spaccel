<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_095257_create_table_keywords_expert
 */
class m230104_095257_create_table_keywords_expert extends Migration
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

        $this->createTable('keywords_expert', [
            'id' => $this->primaryKey(11)->unsigned(),
            'expert_id' => $this->integer(11)->notNull(),
            'description' => $this->text(),
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
