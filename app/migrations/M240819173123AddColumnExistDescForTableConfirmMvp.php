<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240819173123AddColumnExistDescForTableConfirmMvp
 */
class M240819173123AddColumnExistDescForTableConfirmMvp extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_mvp', 'exist_desc', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->dropColumn('confirm_mvp', 'exist_desc');
    }
}
