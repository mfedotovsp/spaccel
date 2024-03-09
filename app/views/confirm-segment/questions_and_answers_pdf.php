<?php

use app\models\AnswersQuestionsConfirmSegment;
use app\models\QuestionsConfirmSegment;
use app\models\QuestionStatus;
use app\models\RespondsSegment;
use yii\helpers\Html;

/**
 * @var QuestionsConfirmSegment[] $questions
 */

?>

<!--Ответы респондентов на вопросы интервью-->
<?php foreach ($questions as $i => $question) : ?>

    <table>

        <tr>
            <td colspan="2" style="color: #ffffff; background: #707F99; font-size: 18px; margin: 2px 0; padding: 10px;">

                Вопрос <?= ($i+1) ?>: <?= $question->getTitle() ?>

                <?php if ($question->getStatus() === QuestionStatus::STATUS_NOT_STAR) : ?>
                    <?= Html::img('/web/images/icons/icon_gray_star.png', ['style' => ['width' => '20px']]) ?>
                <?php elseif ($question->getStatus() === QuestionStatus::STATUS_ONE_STAR) : ?>
                    <?= Html::img('/web/images/icons/icon_golden_star.png', ['style' => ['width' => '20px']]) ?>
                <?php endif; ?>

            </td>
        </tr>

        <?php
        /** @var $answers AnswersQuestionsConfirmSegment[] */
        $answers = AnswersQuestionsConfirmSegment::find(false)
            ->andWhere(['question_id' => $question->getId()])
            ->all();

        foreach ($answers as $answer) : ?>

            <?php
            /** @var $respond RespondsSegment */
            $respond = RespondsSegment::find(false)
                ->andWhere(['id' => $answer->getRespondId()])
                ->one();
            ?>

            <tr style="color: #4F4F4F; background: #F2F2F2;">
                <td style="width: 200px; font-size: 16px; padding: 10px;"><?= $respond->getName() ?></td>
                <td style="width: 480px; font-size: 13px; padding: 10px;"><?= $answer->getAnswer() ?></td>
            </tr>

        <?php endforeach; ?>

    </table>

<?php endforeach; ?>



