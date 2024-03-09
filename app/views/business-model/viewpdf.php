<?php

use app\models\BusinessModel;
use app\models\Segments;

$this->title = 'Бизнес-модель';

/**
 * @var BusinessModel $model
 */

?>

<div class="business-model-view-export">

    <h2><?= $this->title ?></h2>

    <table>

        <tr>
            <td rowspan="2" class="block-200-export">
                <h5 style="text-transform: uppercase;">Ключевые партнеры</h5>
                <?= $model->getPartners() ?>
            </td>
            <td rowspan="" class="block-200-export">
                <h5 style="text-transform: uppercase;">Ключевые направления</h5>

                <div class="export_business_model_mini_header">Тип взаимодейстивия с рынком:</div>
                <?php
                if ($model->segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) {
                    echo 'В2С (бизнес-клиент)';
                } else {
                    echo 'B2B (бизнес-бизнес)';
                }
                ?>

                <div class="export_business_model_mini_header">Сфера деятельности:</div>
                <?= $model->segment->getFieldOfActivity() ?>

                <div class="export_business_model_mini_header">Вид / специализация деятельности:</div>
                <?= $model->segment->getSortOfActivity() ?>

            </td>
            <td rowspan="2" class="block-200-export">
                <h5 style="text-transform: uppercase;">Ценностное предложение</h5><?= $model->gcp->getDescription() ?>
            </td>
            <td rowspan="" class="block-200-export">
                <h5 style="text-transform: uppercase;">Взаимоотношения с клиентами</h5><?= $model->getRelations() ?>
            </td>
            <td rowspan="2" class="block-200-export">

                <h5 style="text-transform: uppercase;">Потребительский сегмент</h5>

                <div class="export_business_model_mini_header">Наименование:</div>
                <?= $model->segment->getName() ?>

                <div class="export_business_model_mini_header">Краткое описание:</div>
                <?= $model->segment->getDescription() ?>

                <div class="export_business_model_mini_header">Потенциальное количество потребителей:</div>
                <?= number_format($model->segment->getQuantity() * 1000, 0, '', ' ') . ' человек' ?>

                <?php if ($model->segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C): ?>
                    <div class="mini_header_desc_block">Потенциальное количество потребителей:</div>
                    <?= number_format($model->segment->getQuantity(), 0, '', ' ') . ' человек' ?>
                <?php else: ?>
                    <div class="mini_header_desc_block">Потенциальное количество представителей сегмента:</div>
                    <?= number_format($model->segment->getQuantity(), 0, '', ' ') . ' ед.' ?>
                <?php endif; ?>

                <div class="export_business_model_mini_header">Объем рынка:</div>
                <?= number_format($model->segment->getMarketVolume() * 1000000, 0, '', ' ') . ' рублей' ?>

            </td>
        </tr>

        <tr>
            <td rowspan="" class="block-200-export">
                <h5 style="text-transform: uppercase;">Ключевые ресурсы</h5><?= $model->getResources() ?>
            </td>
            <td rowspan="" class="block-200-export">
                <h5 style="text-transform: uppercase;">Каналы коммуникации и сбыта</h5><?= $model->getDistributionOfSales() ?>
            </td>
        </tr>

    </table>

    <table>
        <tr>
            <td colspan="" class="block-100-export">
                <h5 style="text-transform: uppercase;">Структура издержек</h5><?= $model->getCost() ?>
            </td>
            <td colspan="" class="block-100-export">
                <h5 style="text-transform: uppercase;">Потоки поступления доходов</h5><?= $model->getRevenue() ?>
            </td>
        </tr>
    </table>

</div>