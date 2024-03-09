<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230211_121110_create_table_segment_requirement
 */
class m230211_121110_create_table_segment_requirement extends Migration
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

        $this->createTable('segment_requirement', [
            'segment_id' => $this->integer(11)->notNull(),
            'requirement_id' => $this->integer(11)->notNull(),
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
