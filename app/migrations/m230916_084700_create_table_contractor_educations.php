<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230916_084700_create_table_contractor_educations
 */
class m230916_084700_create_table_contractor_educations extends Migration
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

        $this->createTable('contractor_educations', [
            'id' => $this->primaryKey(11)->unsigned(),
            'contractor_id' => $this->integer(11)->notNull(),
            'educational_institution' => $this->string(255)->notNull(),
            'faculty' => $this->string(255)->notNull(),
            'course' => $this->string(255),
            'finish_date' => $this->integer(11),
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
