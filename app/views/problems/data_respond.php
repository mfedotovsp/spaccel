<?php

use app\models\InterviewConfirmSegment;
use app\models\RespondsSegment;
use yii\helpers\Html;
use app\models\StageConfirm;
use app\models\QuestionStatus;

/**
 * @var RespondsSegment $respond
 * @var InterviewConfirmSegment $interview
 */

?>

<div class="row" style="margin-bottom: 15px; margin-top: 15px; color: #4F4F4F;">

    <div class="col-md-12" style="padding: 0 20px;">
        <div style="font-weight: 700;">Респондент</div>
        <div><?= $respond->getName() ?></div>
    </div>

    <div class="col-md-12" style="padding: 0 20px; font-size: 24px; margin-top: 5px;">
        <div style="border-bottom: 1px solid #ccc;">Ответы на вопросы интервью</div>
    </div>

    <?php foreach ($respond->answers as $index => $answer) : ?>

        <div class="col-md-12" style="padding: 0 20px; margin-top: 10px;">

            <?php if ($answer->question->getStatus() === QuestionStatus::STATUS_ONE_STAR) : ?>
                <div style="font-weight: 700; color: #52be7f;"><?= $answer->question->getTitle() ?></div>
            <?php elseif($answer->question->getStatus() === QuestionStatus::STATUS_NOT_STAR) : ?>
                <div style="font-weight: 700;"><?= $answer->question->getTitle() ?></div>

            <?php endif; ?>
            <div><?= $answer->getAnswer() ?></div>
        </div>

    <?php endforeach; ?>

    <div class="col-md-12" style="padding: 0 20px; margin-bottom: 10px; margin-top: 10px;">
        <div style="font-weight: 700; border-top: 1px solid #ccc; padding-top: 10px;">Варианты проблем</div>
        <div><?= $interview->getResult() ?></div>
    </div>

    <div class="col-md-12">

        <p style="padding-left: 5px; font-weight: 700;">Приложенный файл</p>

        <?php if (!empty($interview->getInterviewFile())) : ?>

            <div style="margin-top: -5px; margin-bottom: 30px;">

                <div style="display: flex; align-items: center;">

                    <?= Html::a('Скачать файл', ['/interviews/download', 'stage' => StageConfirm::STAGE_CONFIRM_SEGMENT, 'id' => $interview->getId()], [
                        'target' => '_blank',
                        'class' => "btn btn-success interview_file_view-" . $interview->getId(),
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'color' => '#FFFFFF',
                            'justify-content' => 'center',
                            'background' => '#52BE7F',
                            'width' => '180px',
                            'height' => '40px',
                            'font-size' => '16px',
                            'border-radius' => '8px',
                            'text-transform' => 'uppercase',
                            'font-weight' => '700',
                            'padding-top' => '9px'
                        ],

                    ]) ?>

                </div>

                <div class="title_name_update_form" style="padding-left: 5px; padding-top: 5px; margin-bottom: -10px;"><?= $interview->getInterviewFile() ?></div>

            </div>

        <?php endif;?>

        <?php if (empty($interview->getInterviewFile())) : ?>

            <div class="col-md-12" style="padding-left: 5px; margin-bottom: 20px; margin-top: -10px;">Файл не выбран</div>

        <?php endif;?>

    </div>

</div>
