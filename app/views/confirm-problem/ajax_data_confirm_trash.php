<?php

use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use app\models\User;
use yii\helpers\Html;

/**
 * @var ConfirmProblem $model
 * @var Problems $problem
 * @var Projects $project
 * @var Segments $segment
 * @var ConfirmSegment $segmentConfirm
 * @var int $countContractorResponds
 */

$project = Projects::find(false)->andWhere(['id' => $problem->getProjectId()])->one();
$segment = Segments::find(false)->andWhere(['id' => $problem->getSegmentId()])->one();
$segmentConfirm = ConfirmSegment::find(false)->andWhere(['segment_id' => $segment->getId()])->one();

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
            <div class="col-md-12"><?= $segmentConfirm->getGreetingInterview() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Информация о вас для респондентов</div>
            <div class="col-md-12"><?= $segmentConfirm->getViewInterview() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
            <div class="col-md-12"><?= $segmentConfirm->getReasonInterview() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Формулировка проблемы, которую проверяем</div>
            <div class="col-md-12"><?= $problem->getDescription() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Показатель прохождения теста</div>
            <div class="col-md-12">К = <?= $problem->getIndicatorPositivePassage() ?> %</div>
        </div>

        <div class="row">
            <div class="col-md-12">Вопросы для проверки гипотезы проблемы и ответы на них:</div>
            <div class="col-md-12"><?= $problem->getListExpectedResultsInterview() ?></div>
        </div>

        <div class="row">
            <div class="col-md-12">Потребность потребителя сегмента, которую проверяем</div>
            <div class="col-md-12"><?= $model->getNeedConsumer() ?></div>
        </div>

        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])): ?>
            <div class="row">
                <div class="col-md-12">Количество респондентов, занятых исполнителями:
                    <span><?= $countContractorResponds ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">Количество респондентов (представителей сегмента):
                <span><?= $model->getCountRespond() ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">Необходимое количество респондентов, подтверждающих проблему:
                <span><?= $model->getCountPositive() ?></span>
            </div>
        </div>

    </div>

</div>
