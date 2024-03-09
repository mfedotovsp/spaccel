<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m221225_124232_create_table_answers_questions_confirm_gcp
 */
class m221225_124232_create_table_answers_questions_confirm_gcp extends Migration
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

        $this->createTable('answers_questions_confirm_gcp', [
            'id' => $this->primaryKey(11)->unsigned(),
            'question_id' => $this->integer(11)->notNull(),
            'respond_id' => $this->integer(11)->notNull(),
            'answer' => $this->text()
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
