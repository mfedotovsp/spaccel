<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m231029_170913_add_primary_key_for_table_contractor_project
 */
class m231029_170913_add_primary_key_for_table_contractor_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addPrimaryKey('pk_contractor_project', 'contractor_project', ['contractor_id', 'project_id', 'activity_id']);
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return false;
    }
}
