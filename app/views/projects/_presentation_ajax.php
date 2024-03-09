<?php

use app\models\Projects;
use yii\helpers\Html;

/** @var Projects $project */

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

$string .= '<div class="row container-fluid" style="color: #4F4F4F;">';

$string .= '<div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Описание проекта</div></div>
                    <div style="font-weight: 700;">Сокращенное наименование проекта</div><div style="margin-bottom: 10px;">'.$project->getProjectName().'</div>
                    <div style="font-weight: 700;">Полное наименование проекта</div><div style="margin-bottom: 10px;">'.$project->getProjectFullname().'</div>
                    <div style="font-weight: 700;">Описание проекта</div><div style="margin-bottom: 10px;">'.$project->getDescription().'</div>
                    <div style="font-weight: 700;">Цель проекта</div><div style="margin-bottom: 20px;">'.$project->getPurposeProject().'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Результат интеллектуальной деятельности</div></div>
                    <div style="font-weight: 700;">Результат интеллектуальной деятельности</div><div style="margin-bottom: 10px;">'.$project->getRid().'</div>
                    <div style="font-weight: 700;">Суть результата интеллектуальной деятельности</div><div style="margin-bottom: 20px;">'.$project->getCoreRid().'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Сведения о патенте</div></div>
                    <div style="font-weight: 700;">Наименование патента</div><div style="margin-bottom: 10px;">'.$patent_name.'</div>
                    <div style="font-weight: 700;">Номер патента</div><div style="margin-bottom: 10px;">'.$patent_number.'</div>
                    <div style="font-weight: 700;">Дата получения патента</div><div style="margin-bottom: 20px;">'.$patent_date.'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Авторы проекта</div></div>
                    <div style="margin-bottom: 10px;">'.$project->showListAuthors().'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Исполнители проекта</div></div>
                    <div style="margin-bottom: 10px;">'.$listContractors.'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Сведения о технологии</div></div>
                    <div style="font-weight: 700;">На какой технологии основан проект</div><div style="margin-bottom: 10px;">'.$project->getTechnology().'</div>
                    <div style="font-weight: 700;">Макет базовой технологии</div><div style="margin-bottom: 20px;">'.$layout_technology.'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Регистрация юридического лица</div></div>
                    <div style="font-weight: 700;">Зарегистрированное юр. лицо</div><div style="margin-bottom: 10px;">'.$register_name.'</div>
                    <div style="font-weight: 700;">Дата регистрации</div><div style="margin-bottom: 20px;">'.$register_date.'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Адрес сайта</div></div>
                    <div style="margin-bottom: 20px;">'.$site.'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Инвестиции в проект</div></div>
                    <div style="font-weight: 700;">Инвестор</div><div style="margin-bottom: 10px;">'.$invest_name.'</div>
                    <div style="font-weight: 700;">Сумма инвестиций</div><div style="margin-bottom: 10px;">'.$invest_amount.'</div>
                    <div style="font-weight: 700;">Дата получения инвестиций</div><div style="margin-bottom: 20px;">'.$invest_date.'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Анонс проекта</div></div>
                    <div style="font-weight: 700;">Мероприятие, на котором проект анонсирован впервые</div><div style="margin-bottom: 10px;">'.$announcement_event.'</div>
                    <div style="font-weight: 700;">Дата анонсирования проекта</div><div style="margin-bottom: 20px;">'.$date_of_announcement.'</div>
                    
                    <div class="panel panel-default"><div class="panel-heading" style="font-size: 24px;">Презентационные файлы</div></div>';

$string .= '<div style="margin-bottom: 20px;">';

if (!empty($project->preFiles)) {
    foreach ($project->preFiles as $file) {
        $filename = $file->getFileName();
        if (mb_strlen($filename) > 35) {
            $filename = mb_substr($file->getFileName(), 0, 35) . '...';
        }
        $string .= '<div style="display: flex; margin: 2px 0; align-items: center;" class="one_block_file-' . $file->getId() . '">' .
            Html::a('<div style="display:flex; width: 100%; justify-content: space-between;"><div>' . $filename . '</div><div>' . Html::img('/images/icons/icon_export.png', ['style' => ['width' => '22px']]) . '</div></div>', ['/projects/download', 'id' => $file->getId()], [
                'title' => 'Скачать файл',
                'class' => 'btn btn-default prefiles',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#E0E0E0',
                    'width' => '320px',
                    'height' => '40px',
                    'text-align' => 'left',
                    'font-size' => '14px',
                    'border-radius' => '8px',
                    'margin-right' => '5px',
                ]
            ]) . '</div>';
    }
} else {
    $string .= $default_value;
}

$string .= '</div></div>';

echo $string;