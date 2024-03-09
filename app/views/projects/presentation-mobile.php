<?php

use app\models\PreFiles;
use app\models\Projects;
use yii\helpers\Html;

/** @var Projects $project */

$this->title = 'Презентация проекта';

$string = '';
$default_value = '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _';

$patent_name = $project->getPatentName() ?: $default_value;
$patent_number = $project->getPatentNumber() ?: $default_value;
$patent_date = $project->getPatentDate() ? date('d.m.Y', $project->getPatentDate()) : $default_value;
$layout_technology = $project->getLayoutTechnology() ?: $default_value;
$site = $project->getSite() ?: $default_value;
$register_name = $project->getRegisterName() ?: $default_value;
$register_date = $project->getRegisterDate() ? date('d.m.Y', $project->getRegisterDate()) : $default_value;
$invest_name = $project->getInvestName() ?: $default_value;
$invest_amount = $project->getInvestAmount() ? number_format($project->getInvestAmount(), 0, '', ' ') . ' руб.' : $default_value;
$invest_date = $project->getInvestDate() ? date('d.m.Y', $project->getInvestDate()) : $default_value;
$announcement_event = $project->getAnnouncementEvent() ?: $default_value;
$date_of_announcement = $project->getDateOfAnnouncement() ? date('d.m.Y', $project->getDateOfAnnouncement()) : $default_value;

?>

<div class="row">
    <div class="col-xs-12 header-title-mobile"><?= $this->title ?></div>
</div>

<div class="presentation-mobile">

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Описание проекта</div>
        <div class="presentation-mobile-title-row">Сокращенное наименование проекта</div>
        <div class="presentation-mobile-simple-row"><?= $project->getProjectName() ?></div>
        <div class="presentation-mobile-title-row">Полное наименование проекта</div>
        <div class="presentation-mobile-simple-row"><?= $project->getProjectFullname() ?></div>
        <div class="presentation-mobile-title-row">Описание проекта</div>
        <div class="presentation-mobile-simple-row"><?= $project->getDescription() ?></div>
        <div class="presentation-mobile-title-row">Цель проекта</div>
        <div class="presentation-mobile-simple-row"><?= $project->getPurposeProject() ?></div>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Результат интеллектуальной деятельности</div>
        <div class="presentation-mobile-title-row">Результат интеллектуальной деятельности</div>
        <div class="presentation-mobile-simple-row"><?= $project->getRid() ?></div>
        <div class="presentation-mobile-title-row">Суть результата интеллектуальной деятельности</div>
        <div class="presentation-mobile-simple-row"><?= $project->getCoreRid() ?></div>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Сведения о патенте</div>
        <div class="presentation-mobile-title-row">Наименование патента</div>
        <div class="presentation-mobile-simple-row"><?= $patent_name ?></div>
        <div class="presentation-mobile-title-row">Номер патента</div>
        <div class="presentation-mobile-simple-row"><?= $patent_number ?></div>
        <div class="presentation-mobile-title-row">Дата получения патента</div>
        <div class="presentation-mobile-simple-row"><?= $patent_date ?></div>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Команда проекта</div>
        <?= $project->showListAuthors(true) ?>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Исполнители проекта</div>
        <?= $project->showListContractors(true) ?>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Сведения о технологии</div>
        <div class="presentation-mobile-title-row">На какой технологии основан проект</div>
        <div class="presentation-mobile-simple-row"><?= $project->getTechnology() ?></div>
        <div class="presentation-mobile-title-row">Макет базовой технологии</div>
        <div class="presentation-mobile-simple-row"><?= $layout_technology ?></div>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Регистрация юридического лица</div>
        <div class="presentation-mobile-title-row">Зарегистрированное юр. лицо</div>
        <div class="presentation-mobile-simple-row"><?= $register_name ?></div>
        <div class="presentation-mobile-title-row">Дата регистрации</div>
        <div class="presentation-mobile-simple-row"><?= $register_date ?></div>
        <div class="presentation-mobile-title-row">Адрес сайта</div>
        <div class="presentation-mobile-simple-row"><?= $site ?></div>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Инвестиции в проект</div>
        <div class="presentation-mobile-title-row">Инвестор</div>
        <div class="presentation-mobile-simple-row"><?= $invest_name ?></div>
        <div class="presentation-mobile-title-row">Сумма инвестиций</div>
        <div class="presentation-mobile-simple-row"><?= $invest_amount ?></div>
        <div class="presentation-mobile-title-row">Дата получения инвестиций</div>
        <div class="presentation-mobile-simple-row"><?= $invest_date ?></div>
    </div>

    <div class="presentation-mobile-one-stage">
        <div class="presentation-mobile-header-stage">Анонс проекта</div>
        <div class="presentation-mobile-title-row">Мероприятие, на котором проект анонсирован впервые</div>
        <div class="presentation-mobile-simple-row"><?= $announcement_event ?></div>
        <div class="presentation-mobile-title-row">Дата анонсирования проекта</div>
        <div class="presentation-mobile-simple-row"><?= $date_of_announcement ?></div>
        <div class="presentation-mobile-title-row">Презентационные файлы</div>
        <?php
        /** @var $preFiles PreFiles[] */
        $preFiles = PreFiles::find(false)
            ->andWhere(['project_id' => $project->getId()])
            ->all();

        if (!empty($preFiles)): ?>
            <?php foreach ($preFiles as $file): ?>
                <div class="presentation-mobile-simple-row">
                    <?= Html::a(presentation - mobile . phpHtml::img('/images/icons/icon_export_pdf.png', ['style' => ['width' => '17px', 'margin-right' => '10px']]), ['/projects/download', 'id' => $file->getId()]) ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="presentation-mobile-simple-row"><?= $default_value ?></div>
        <?php endif; ?>
    </div>

</div>


<div class="row">
    <div class="col-md-12" style="display:flex;justify-content: center;">
        <?= Html::button('Закрыть', [
            'onclick' => 'javascript:history.back()',
            'class' => 'btn button-close-result-mobile'
        ]) ?>
    </div>
</div>
