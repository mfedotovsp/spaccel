<?php

use app\models\Projects;

/**
 * @var Projects $project
 */


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
$listContractors = $project->showListContractors() ?: 'Отсутствуют';

?>

<div class="project-view-export">

    <!--Описание проекта-->
    <div class="header_section">Описание проекта</div>

    <div class="section_content">
        <h4>Сокращенное наименование проекта</h4>
        <div><?= $project->getProjectName() ?></div>
        <h4>Полное наименование проекта</h4>
        <div><?= $project->getProjectFullname() ?></div>
        <h4>Описание проекта</h4>
        <div><?= $project->getDescription() ?></div>
        <h4>Цель проекта</h4>
        <div><?= $project->getPurposeProject() ?></div>
    </div>

    <!--Результат интеллектуальной деятельности-->
    <div class="header_section">Результат интеллектуальной деятельности</div>

    <div class="section_content">
        <h4>Результат интеллектуальной деятельности</h4>
        <div><?= $project->getRid() ?></div>
        <h4>Суть результата интеллектуальной деятельности</h4>
        <div><?= $project->getCoreRid() ?></div>
    </div>

    <!--Сведения о патенте-->
    <div class="header_section">Сведения о патенте</div>

    <div class="section_content">
        <h4>Наименование патента</h4>
        <div><?= $patent_name ?></div>
        <h4>Номер патента</h4>
        <div><?= $patent_number ?></div>
        <h4>Дата получения патента</h4>
        <div><?= $patent_date ?></div>
    </div>

    <!--Авторы проекта-->
    <div class="header_section">Авторы проекта</div>
    
    <div class="section_content">
        <?= $project->showListAuthors() ?>
    </div>

    <!--Исполнители проекта-->
    <div class="header_section">Исполнители проекта</div>

    <div class="section_content">
        <?= $listContractors ?>
    </div>

    <!--Сведения о технологии-->
    <div class="header_section">Сведения о технологии</div>

    <div class="section_content">
        <h4>На какой технологии основан проект</h4>
        <div><?= $project->getTechnology() ?></div>
        <h4>Макет базовой технологии</h4>
        <div><?= $layout_technology ?></div>
    </div>

    <!--Регистрация юридического лица-->
    <div class="header_section">Регистрация юридического лица</div>

    <div class="section_content">
        <h4>Зарегистрированное юр. лицо</h4>
        <div><?= $register_name ?></div>
        <h4>Дата регистрации</h4>
        <div><?= $register_date ?></div>
    </div>

    <!--Адрес сайта-->
    <div class="header_section">Адрес сайта</div>

    <div class="section_content">
        <div><?= $site ?></div>
    </div>

    <!--Инвестиции в проект-->
    <div class="header_section">Инвестиции в проект</div>

    <div class="section_content">
        <h4>Инвестор</h4>
        <div><?= $invest_name ?></div>
        <h4>Сумма инвестиций</h4>
        <div><?= $invest_amount ?></div>
        <h4>Дата получения инвестиций</h4>
        <div><?= $invest_date ?></div>
    </div>

    <!--Анонс проекта-->
    <div class="header_section">Анонс проекта</div>

    <div class="section_content">
        <h4>Мероприятие, на котором проект анонсирован впервые</h4>
        <div><?= $announcement_event ?></div>
        <h4>Дата анонсирования проекта</h4>
        <div><?= $date_of_announcement ?></div>
    </div>

</div>
