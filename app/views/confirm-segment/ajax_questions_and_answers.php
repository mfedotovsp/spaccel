<?php

use app\models\AnswersQuestionsConfirmSegment;
use app\models\QuestionsConfirmSegment;
use app\models\QuestionStatus;
use app\models\RespondsSegment;

/**
 * @var QuestionsConfirmSegment[] $questions
 * @var AnswersQuestionsConfirmSegment $answer
 * @var bool $isOnlyNotDelete
 */

?>

<!--Ответы респондентов на вопросы интервью-->
<div class="container-questions-and-answers">

<?php foreach ($questions as $i => $question) : ?>

    <div class="row container-fluid question-container">
        <div class="col-md-12">

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
    </div>

    <?php
    /** @var $answers AnswersQuestionsConfirmSegment[] */
    $answers = $isOnlyNotDelete ?
        $question->answers :
        AnswersQuestionsConfirmSegment::find(false)
            ->andWhere(['question_id' => $question->getId()])
            ->all();

    foreach ($answers as $answer) : ?>

        <?php
        /** @var $respond RespondsSegment */
        $respond = $isOnlyNotDelete ?
            $answer->respond :
            RespondsSegment::find(false)
                ->andWhere(['id' => $answer->getRespondId()])
                ->one();
        ?>

        <div class="row container-fluid answer-container">
            <div class="col-md-4 col-lg-3 respond-block"><?= $respond->getName() ?></div>
            <div class="col-md-8 col-lg-9"><?= $answer->getAnswer() ?></div>
        </div>

    <?php endforeach; ?>

<?php endforeach; ?>

</div>