<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230104_122937_create_table_segments
 */
class m230104_122937_create_table_segments extends Migration
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

        $this->createTable('segments', [
            'id' => $this->primaryKey(11)->unsigned(),
            'project_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'type_of_interaction_between_subjects' => $this->integer(11),
            'field_of_activity' => $this->string(255),
            'sort_of_activity' => $this->string(255),
            'age_from' => $this->integer(11),
            'age_to' => $this->integer(11),
            'gender_consumer' => $this->integer(11),
            'education_of_consumer' => $this->integer(11),
            'income_from' => $this->integer(11),
            'income_to' => $this->integer(11),
            'quantity' => $this->integer(11),
            'market_volume' => $this->integer(11),
            'company_products' => $this->text(),
            'company_partner' => $this->text(),
            'add_info' => $this->text(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
            'time_confirm' => $this->integer(11),
            'exist_confirm' => $this->integer(11),
            'enable_expertise' => "ENUM('0', '1') NOT NULL DEFAULT '0'"
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
