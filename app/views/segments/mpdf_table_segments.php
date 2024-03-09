<?php

use app\models\ConfirmSegment;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use app\models\Segments;

/**
 * @var Segments[] $models
 */

?>

<div class="segment-index-export">

    <!--Заголовки для списка сегментов-->
    <table class="all_headers_data_segments">

        <tr>
            <td class="block_segment_name" colspan="2">Наименование сегмента</td>
            <td class="block_segment_type">Тип</td>
            <td class="block_segment_params">Сфера деятельности</td>
            <td class="block_segment_params">Вид / специализация деятельности</td>
            <td class="block_segment_market_volume">
                <div>Платеже- способность</div>
                <div>млн. руб./год</div>
            </td>
        </tr>

    </table>

    <table class="block_all_segments">

        <?php foreach ($models as $model) : ?>

            <?php
            /** @var $confirm ConfirmSegment */
            $confirm = ConfirmSegment::find(false)
                ->andWhere(['segment_id' => $model->getId()])
                ->one();
            ?>

        <tr>

            <td class="block_segment_status">
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

            <td class="block_segment_name"><?= $model->getName() ?></td>

            <td class="block_segment_type">
                <?php

                if ($model->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) {
                    echo '<div class="">B2C</div>';
                }
                elseif ($model->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2B) {
                    echo '<div class="">B2B</div>';
                }

                ?>
            </td>

            <td class="block_segment_params"><?= $model->getFieldOfActivity() ?></td>
            <td class="block_segment_params"><?= $model->getSortOfActivity() ?></td>
            <td class="block_segment_market_volume"><?= number_format($model->getMarketVolume(), 0, '', ' ') ?></td>

        </tr>

        <?php endforeach; ?>

    </table>

</div>