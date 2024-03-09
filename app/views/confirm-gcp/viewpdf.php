<?php

use app\models\ConfirmGcp;
use app\models\InterviewConfirmGcp;
use app\models\RespondsGcp;
use yii\helpers\Html;

/**
 * @var ConfirmGcp $model
 * @var RespondsGcp[] $responds
 */

?>

<!--Css Style for PDF-->
<!--https://mpdf.github.io/css-stylesheets/supported-css.html-->

<div class="">


    <table>

        <tr style="background: #F2F2F2;">
            <td colspan="1" style="width: 50px;"></td>
            <td colspan="3" style="width: 265px; padding: 15px 5px; color: #4F4F4F; text-align: center;">
                <strong>Фамилия, имя, отчество</strong>
            </td>
            <td colspan="3" style="width: 265px; padding: 15px 5px; color: #4F4F4F; text-align: center;">
                <strong>Данные респондента</strong>
            </td>
            <td colspan="3" style="width: 265px; padding: 15px 5px; color: #4F4F4F; text-align: center;">
                <strong>Место проведения</strong>
            </td>
            <td colspan="2" style="width: 200px; padding: 15px 5px; color: #4F4F4F; text-align: center;">
                <strong>Интервью</strong>
            </td>
        </tr>


        <tr style="background: #F2F2F2; ">
            <td colspan="1" style="width: 50px;"></td>
            <td colspan="3" style="width: 265px; padding: 10px 5px; color: #4F4F4F; text-align: center;">

            </td>
            <td colspan="3" style="width: 265px; padding: 10px 5px; color: #4F4F4F; font-size: 12px; text-align: center;">
                Кто? Откуда? Чем занят?
            </td>
            <td colspan="3" style="width: 265px; padding: 10px 5px; color: #4F4F4F; font-size: 12px; text-align: center;">
                Организация, адрес
            </td>
            <td colspan="1" style="width: 100px; padding: 10px 5px; color: #4F4F4F; text-align: center; font-size: 12px;">
                План
            </td>
            <td colspan="1" style="width: 100px; padding: 10px 5px; color: #4F4F4F; text-align: center; font-size: 12px;">
                Факт
            </td>
        </tr>


        <?php foreach ($responds as $respond): ?>

            <tr class="row container-one_respond" style="background: #707F99;">

                <td colspan="1" style="width: 50px; text-align: center;">
                    <?php
                    /** @var $interview InterviewConfirmGcp */
                    $interview = InterviewConfirmGcp::find(false)
                        ->andWhere(['respond_id' => $respond->getId()])
                        ->one();

                    if ($interview) {
                        if ($interview->getStatus() === 1) {
                            echo  Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px', 'margin-bottom' => '-4px']]);
                        }
                        elseif ($interview->getStatus() === 0) {
                            echo  Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px', 'margin-bottom' => '-4px']]);
                        }
                    }
                    else {
                        echo  Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px', 'margin-bottom' => '-4px']]);
                    }
                    ?>
                </td>

                <td colspan="3" style="width: 265px; padding: 15px 5px; color: #FFFFFF; font-size: 16px; text-align: center;">
                    <?=  $respond->getName() ?>
                </td>

                <td colspan="3" style="width: 265px; padding: 15px 5px; color: #FFFFFF; font-size: 12px;">
                    <?php if ($respond->getInfoRespond()) : ?>
                        <?= $respond->getInfoRespond() ?>
                    <?php endif; ?>
                </td>

                <td colspan="3" style="width: 265px; padding: 15px 5px; color: #FFFFFF; font-size: 12px;">
                    <?php if ($respond->getPlaceInterview()) : ?>
                        <?= $respond->getPlaceInterview() ?>
                    <?php endif; ?>
                </td>

                <td colspan="1" style="width: 100px; padding: 15px 5px; text-align: center; color: #FFFFFF; font-size: 15px;">
                    <?php if ($respond->getDatePlan()) : ?>
                        <?= date("d.m.y", $respond->getDatePlan()) ?>
                    <?php endif; ?>
                </td>

                <td colspan="1" style="width: 100px; padding: 15px 5px; text-align: center; color: #FFFFFF; font-size: 15px;">
                    <?php if ($interview) : ?>
                        <?= date("d.m.y", $interview->getUpdatedAt()) ?>
                    <?php endif; ?>
                </td>

            </tr>

        <?php  endforeach;?>

    </table>

</div>
