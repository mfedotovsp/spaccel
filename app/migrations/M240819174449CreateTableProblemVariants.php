<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240819174449CreateTableProblemVariants
 */
class M240819174449CreateTableProblemVariants extends Migration
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

        $this->createTable('problem_variants', [
            'id' => $this->primaryKey(11)->unsigned(),
            'description_id' => $this->integer(11)->notNull(),
            'confirm_id' => $this->integer(11)->notNull(),
            'description' => $this->text(),
        ], $tableOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->delete('problem_variants');
    }
}
