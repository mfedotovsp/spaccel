<?php

use app\models\ConfirmSegment;
use app\models\Projects;
use app\models\QuestionsConfirmSegment;
use app\models\QuestionStatus;
use app\models\Segments;
use yii\helpers\Html;

/**
 * @var bool $isTaskMarketing
 * @var ConfirmSegment $model
 * @var Segments $segment
 * @var Projects $project
 * @var QuestionsConfirmSegment[] $questions
 * @var int $countContractorResponds
 */

?>

<div class="block_export_link_hypothesis">

    <?= Html::a('<div style="margin-top: -15px;">Исходные данные подтверждения сегмента' . '</div>', ['#'], [
        'class' => 'export_link_hypothesis', 'style' => ['cursor' => 'default'], 'onclick' => 'return false;'
    ]) ?>

</div>

<div class="row container-fluid" style="color: #4F4F4F;">

    <div class="row">
        <div class="col-md-12 bolder">Цель проекта</div>
        <div class="col-md-12"><?= $project->getPurposeProject() ?></div>
    </div>

    <div class="row">
        <div class="col-md-12 bolder">Приветствие в начале встречи</div>
        <div class="col-md-12"><?= $model->getGreetingInterview() ?></div>
    </div>

    <div class="row">
        <div class="col-md-12 bolder">Информация о вас для респондентов</div>
        <div class="col-md-12"><?= $model->getViewInterview() ?></div>
    </div>

    <div class="row">
        <div class="col-md-12 bolder">Причина и тема (что побудило) для проведения исследования</div>
        <div class="col-md-12"><?= $model->getReasonInterview() ?></div>
    </div>

    <?php if ($isTaskMarketing): ?>

        <div class="row">
            <div class="col-md-12 bolder">Количество респондентов, занятых исполнителями:
                <span style="font-weight: 400;"><?= $countContractorResponds ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 bolder">Планируемое количество респондентов:
                <span style="font-weight: 400;"><?= $model->getCountRespond() ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 bolder">Необходимое количество респондентов, соотв. сегменту:
                <span style="font-weight: 400;"><?= $model->getCountPositive() ?></span>
            </div>
        </div>

    <?php endif; ?>

    <div class="row">
        <div class="col-md-12 text-center">
            <h3>Вопросы для интервью:</h3>
        </div>
        <div class="col md-12">
            <div class="row container-fluid">
                <?php foreach ($questions as $i => $question): ?>
                    <div class="col-md-12" style="min-height: 30px;">

                        Вопрос <?= ($i+1) ?>: <span><?= $question->getTitle() ?></span>

                        <?php if ($question->getStatus() === QuestionStatus::STATUS_NOT_STAR) : ?>
                            <div class="star-passive" title="Значимость вопроса">
                                <div class="star"></div>
                            </div>
                        <?php elseif ($question->getStatus() === QuestionStatus::STATUS_ONE_STAR) : ?>
                            <div class="star-passive" title="Значимость вопроса">
                                <div class="star active"></div>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>
