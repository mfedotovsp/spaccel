<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m230820_082619_insert_into_contractor_activities
 */
class m230820_082619_insert_into_contractor_activities extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $this->batchInsert('contractor_activities', ['title'], [
            ['Маркетинг'],
            ['Техническая разработка'],
            ['Полевая работа'],
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->delete('contractor_activities', ['in', 'title', [
            'Маркетинг', 'Техническая разработка', 'Полевая работа'
        ]]);
    }
}
