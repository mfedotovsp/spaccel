<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240411170311CreateTableEmailLogs
 */
class M240411170311CreateTableEmailLogs extends Migration
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

        $this->createTable('email_logs', [
            'id' => $this->primaryKey(11)->unsigned(),
            'email' => $this->string(255)->notNull(),
            'subject' => $this->string(255)->notNull(),
            'body_html' => $this->text()->notNull(),
            'is_failed' => $this->boolean()->defaultValue(false),
            'error' => $this->text(),
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
