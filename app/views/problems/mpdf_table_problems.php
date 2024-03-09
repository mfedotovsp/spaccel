<?php

use app\models\ConfirmProblem;
use app\models\Problems;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;

/**
 * @var Problems[] $models
 */

?>

<div class="problem-index-export">

    <!--Заголовки для списка проблем-->
    <table class="all_headers_data_problems">

        <tr>
            <td class="block_problem_title" colspan="2">Обознач.</td>
            <td class="block_problem_description">Описание гипотезы проблемы сегмента</td>
            <td class="block_problem_params">Показатель прохождения теста</td>
            <td class="block_problem_date">Дата создания</td>
            <td class="block_problem_date">Дата подтв.</td>
        </tr>

    </table>

    <table class="block_all_problems">

        <?php foreach ($models as $model) : ?>

            <?php
            /** @var $confirm ConfirmProblem */
            $confirm = ConfirmProblem::find(false)
                ->andWhere(['problem_id' => $model->getId()])
                ->one();
            ?>

        <tr>

            <td class="block_problem_status">
                <?php
                if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {

                    echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                }elseif (!$confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                    echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                }elseif ($confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                    echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                }elseif ($model->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {

                    echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                }
                ?>
            </td>

            <td class="block_problem_title"><?= $model->getTitle() ?></td>
            <td class="block_problem_description"><?= $model->getDescription() ?></td>
            <td class="block_problem_params"><?= 'K = ' . $model->getIndicatorPositivePassage() . ' %' ?></td>
            <td class="block_problem_date"><?= date("d.m.y", $model->getCreatedAt()) ?></td>

            <td class="block_problem_date">
                <?php if ($model->getTimeConfirm()) : ?>
                    <?= date("d.m.y", $model->getTimeConfirm()) ?>
                <?php endif; ?>
            </td>

        </tr>

        <?php endforeach; ?>

    </table>

</div>
