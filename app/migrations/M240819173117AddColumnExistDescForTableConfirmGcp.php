<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240819173117AddColumnExistDescForTableConfirmGcp
 */
class M240819173117AddColumnExistDescForTableConfirmGcp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_gcp', 'exist_desc', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->dropColumn('confirm_gcp', 'exist_desc');
    }
}
