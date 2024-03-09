<?php

use app\models\ConfirmSegment;
use app\models\Projects;
use app\models\User;
use yii\helpers\Html;

/**
 * @var ConfirmSegment $model
 * @var Projects $project
 * @var int $countContractorResponds
 */

?>

<div class="container-fluid form-view-data-confirm">

    <div class="row row_header_data">

        <div class="col-sm-12 col-md-9" style="padding: 5px 0 0 0;">
            <?= Html::a('Исходные данные подтверждения' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-segment/get-instruction-step-one'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
            ]) ?>
        </div>

    </div>


    <div class="container-fluid content-view-data-confirm">

        <div class="row">
            <div class="col-md-12">Цель проекта</div>
            <div class="col-md-12"><?= $project->getPurposeProject() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Приветствие в начале встречи</div>
            <div class="col-md-12"><?= $model->getGreetingInterview() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Информация о вас для респондентов</div>
            <div class="col-md-12"><?= $model->getViewInterview() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
            <div class="col-md-12"><?= $model->getReasonInterview() ?></div>
        </div>

        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])): ?>
            <div class="row">
                <div class="col-md-12">Количество респондентов, занятых исполнителями:
                    <span><?= $countContractorResponds ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">Планируемое количество респондентов:
                <span><?= $model->getCountRespond() ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">Необходимое количество респондентов, соотв. сегменту:
                <span><?= $model->getCountPositive() ?></span>
            </div>
        </div>

    </div>

</div>