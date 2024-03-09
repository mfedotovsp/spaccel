<?php

use app\models\ConfirmSegment;
use app\models\QuestionsConfirmSegment;
use app\models\QuestionStatus;
use app\models\Segments;

/**
 * @var QuestionsConfirmSegment[] $questions
 * @var Segments $segment
 * @var ConfirmSegment $model
 */

?>

<!--Список вопросов-->
<div id="QuestionsTable-container" class="row" style="padding-top: 30px; padding-bottom: 30px;">

    <?php foreach ($questions as $q => $question) : ?>

        <div class="col-xs-12 string_question string_question-<?= $question->getId() ?>">

            <div class="row style_form_field_questions">
                <div class="col-xs-8 col-sm-9 col-md-9 col-lg-10">
                    <div style="display:flex;">
                        <div class="number_question" style="padding-right: 15px;"><?= ($q+1) . '. ' ?></div>
                        <div class="title_question"><?= $question->getTitle() ?></div>
                    </div>
                </div>
                <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2 delete_question_link">

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
            </div>

        </div>

    <?php endforeach; ?>

</div>