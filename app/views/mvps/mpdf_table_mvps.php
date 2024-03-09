<?php

use app\models\ConfirmMvp;
use app\models\Mvps;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;

/**
 * @var Mvps[] $models
 */

?>

<div class="mvp-index-export">

    <!--Заголовки для списка MVP-->
    <table class="all_headers_data_mvps">

        <tr>
            <td class="block_mvp_title" colspan="2">Обознач.</td>
            <td class="block_mvp_description">Описание минимально жизнеспособного продукта</td>
            <td class="block_mvp_date">Дата создания</td>
            <td class="block_mvp_date">Дата подтв.</td>
        </tr>

    </table>

    <table class="block_all_mvps">

        <?php foreach ($models as $model) : ?>

            <?php
            /** @var $confirm ConfirmMvp */
            $confirm = ConfirmMvp::find(false)
                ->andWhere(['mvp_id' => $model->getId()])
                ->one();
            ?>

            <tr>

                <td class="block_mvp_status">
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

                <td class="block_mvp_title"><?= $model->getTitle() ?></td>
                <td class="block_mvp_description"><?= $model->getDescription() ?></td>
                <td class="block_mvp_date"><?= date("d.m.y", $model->getCreatedAt()) ?></td>

                <td class="block_mvp_date">
                    <?php if ($model->getTimeConfirm()) : ?>
                        <?= date("d.m.y", $model->getTimeConfirm()) ?>
                    <?php endif; ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>
