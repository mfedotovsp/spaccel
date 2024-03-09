<?php

use app\models\SegmentRequirement;
use app\models\Segments;

/**
 * @var Segments $segment
 */

?>

<div class="segment-view-export">

    <h4>Наименование сегмента</h4>
    <div><?= $segment->getName() ?></div>
    <h4>Краткое описание сегмента</h4>
    <div><?= $segment->getDescription() ?></div>

    <?php if ($segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) : ?>

        <h4>Тип взаимодействия с потребителями</h4>
        <div>Коммерческие взаимоотношения между организацией и частным потребителем (B2C)</div>

        <h4>Сфера деятельности потребителя</h4>
        <div><?= $segment->getFieldOfActivity() ?></div>

        <h4>Вид / специализация деятельности потребителя</h4>
        <div><?= $segment->getSortOfActivity() ?></div>

        <h4>Возраст потребителя</h4>
        <div>
            <?= 'от ' . number_format($segment->getAgeFrom(), 0, '', ' ') . ' до '
            . number_format($segment->getAgeTo(), 0, '', ' ') . ' лет' ?>
        </div>

        <h4>Пол потребителя</h4>
        <div>
            <?php
            if ($segment->getGenderConsumer() === Segments::GENDER_WOMAN) {
                echo 'Женский';
            } elseif ($segment->getGenderConsumer() === Segments::GENDER_MAN) {
                echo 'Мужской';
            } else {
                echo 'Не важно';
            }
            ?>
        </div>

        <h4>Образование потребителя</h4>
        <div>
            <?php
            if ($segment->getEducationOfConsumer() === Segments::SECONDARY_EDUCATION) {
                echo 'Среднее образование';
            }elseif ($segment->getEducationOfConsumer() === Segments::SECONDARY_SPECIAL_EDUCATION) {
                echo 'Среднее образование (специальное)';
            }elseif ($segment->getEducationOfConsumer() === Segments::HIGHER_INCOMPLETE_EDUCATION) {
                echo 'Высшее образование (незаконченное)';
            }elseif ($segment->getEducationOfConsumer() === Segments::HIGHER_EDUCATION) {
                echo 'Высшее образование';
            }else {
                echo '';
            }
            ?>
        </div>

        <h4>Доход потребителя</h4>
        <div>
            <?= 'от ' . number_format($segment->getIncomeFrom(), 0, '', ' ') . ' до '
            . number_format($segment->getIncomeTo(), 0, '', ' ') . ' руб./мес.' ?>
        </div>

        <h4>Потенциальное количество потребителей</h4>
        <div>
            <?= number_format($segment->getQuantity(), 0, '', ' ') . ' человек' ?>
        </div>

    <?php elseif ($segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2B) : ?>

        <h4>Тип взаимодействия с потребителями</h4>
        <div>Коммерческие взаимоотношения между представителями бизнес-аудитории (B2B)</div>

        <?php
        /** @var $segmentRequirement SegmentRequirement */
        $segmentRequirement = SegmentRequirement::find(false)
            ->andWhere(['segment_id' => $segment->getId()])
            ->one();

        if ($segmentRequirement && $requirement = $segmentRequirement->requirement): ?>

            <?php $wishList = $requirement->wishList ?>

            <div class="row mb-5">
                <div class="col-md-12">
                    <h4>Информация о выбранном запросе B2B компаний</h4>
                </div>
            </div>

            <div class="info-requirement">

                <h4>Запрос</h4>
                <div><?= $requirement->getRequirement() ?></div>

                <h4>Причины запроса</h4>
                <?php foreach ($requirement->reasons as $reason): ?>
                    <div>- <?= $reason->getReason() ?></div>
                <?php endforeach; ?>

                <h4>Ожидаемое решение</h4>
                <div><?= $requirement->getExpectedResult() ?></div>

                <?php if ($requirement->getAddInfo() !== ''): ?>
                    <h4>Дополнительная информация о запросе</h4>
                    <div><?= $requirement->getAddInfo() ?></div>
                <?php endif; ?>

                <h4>Наименование предприятия</h4>
                <div><?= $wishList->getCompanyName() ?></div>

                <h4>Тип предприятия</h4>
                <div><?= $wishList->getTypeCompanyName() ?></div>

                <h4>Тип производства</h4>
                <div><?= $wishList->getTypeProductionName() ?></div>

                <h4>Размер предприятия по количеству персонала</h4>
                <div><?= $wishList->getSizeName() ?></div>

                <h4>Локация предприятия</h4>
                <div><?= $wishList->location->getName() ?></div>

                <?php if ($wishList->getAddInfo() !== ''): ?>
                    <h4>Дополнительная информация о предприятии</h4>
                    <div><?= $wishList->getAddInfo() ?></div>
                <?php endif; ?>
            </div>

        <?php endif; ?>

        <h4>Сфера деятельности предприятия</h4>
        <div><?= $segment->getFieldOfActivity() ?></div>

        <h4>Вид / специализация деятельности предприятия</h4>
        <div><?= $segment->getSortOfActivity() ?></div>

        <h4>Продукция / услуги предприятия</h4>
        <div><?= $segment->getCompanyProducts() ?></div>

        <h4>Партнеры предприятия</h4>
        <div><?= $segment->getCompanyPartner() ?></div>

        <h4>Потенциальное количество представителей сегмента</h4>
        <div>
            <?= number_format($segment->getQuantity(), 0, '', ' ') . ' ед.' ?>
        </div>

        <h4>Доход предприятия</h4>
        <div>
            <?= 'от ' . number_format($segment->getIncomeFrom(), 0, '', ' ') . ' до '
            . number_format($segment->getIncomeTo(), 0, '', ' ') . ' млн. руб./год' ?>
        </div>

    <?php endif; ?>


    <h4>Платежеспособность целевого сегмента</h4>
    <div><?= number_format($segment->getMarketVolume(), 0, '', ' ') . ' млн. руб./год' ?></div>


    <?php if (!empty($segment->getAddInfo())) : ?>

        <h4>Дополнительная информация</h4>
        <div><?= $segment->getAddInfo() ?></div>

    <?php endif; ?>

</div>