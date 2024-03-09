<?php

use app\models\RequirementWishList;
use yii\helpers\Html;

/**
 * @var RequirementWishList[] $requirements
 */

?>

<div class="requirementsTable">
    <div class="row headers">
        <div class="col-md-3">Описание запроса</div>
        <div class="col-md-3">Описание ожидаемого решения</div>
        <div class="col-md-3">Причины</div>
        <div class="col-md-2">Дополнительная информация</div>
        <div class="col-md-1"></div>
    </div>

    <?php foreach ($requirements as $key => $requirement): ?>

        <div class="row requirementsDataTable">
            <div class="col-md-3">
                <?= '<span class="bolder">' . ($key+1) . '. </span>' . $requirement->getRequirement() ?>
            </div>

            <div class="col-md-3">
                <?= $requirement->getExpectedResult() ?>
            </div>

            <div class="col-md-3">
                <?php foreach ($requirement->reasons as $reason): ?>
                    <div class="mb-10"> - <?= $reason->getReason() ?></div>
                <?php endforeach; ?>
            </div>

            <div class="col-md-2">
                <?= $requirement->getAddInfo() ?>
            </div>

            <div class="col-md-1">
                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-left' => '20px']]),
                    ['/admin/wish-list/requirement-update', 'id' => $requirement->getId()], [
                        'title' => 'Редактировать',
                    ]) ?>
                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),['/admin/wish-list/requirement-delete', 'id' => $requirement->getId()], [
                    'class' => 'pull-right delete-requirement-wish-list',
                    'title' => 'Удалить',
                ]) ?>
            </div>

        </div>

    <?php endforeach; ?>
</div>
