<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240411171748CreateTableEmailUnsubscribers
 */
class M240411171748CreateTableEmailUnsubscribers extends Migration
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

        $this->createTable('email_unsubscribers', [
            'id' => $this->primaryKey(11)->unsigned(),
            'email' => $this->string(255)->notNull(),
            'deleted_at' => $this->integer(11),
            'created_at' => $this->integer(11)->notNull()
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
