<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class M240819173107AddColumnExistDescForTableConfirmProblem
 */
class M240819173107AddColumnExistDescForTableConfirmProblem extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->addColumn('confirm_problem', 'exist_desc', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->dropColumn('confirm_problem', 'exist_desc');
    }
}
