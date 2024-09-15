<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240819172606AddColumnExistDescForTableConfirmSegment
 */
class M240819172606AddColumnExistDescForTableConfirmSegment extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_segment', 'exist_desc', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->dropColumn('confirm_segment', 'exist_desc');
    }
}
