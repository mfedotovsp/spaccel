<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240819174238CreateTableConfirmSources
 */
class M240819174238CreateTableConfirmSources extends Migration
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

        $this->createTable('confirm_sources', [
            'id' => $this->primaryKey(11)->unsigned(),
            'description_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->delete('confirm_sources');
    }
}
