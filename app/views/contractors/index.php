<?php

use app\models\User;
use yii\helpers\Html;

$this->title = 'Исполнители проектов';
$this->registerCssFile('@web/css/contractors-index-style.css');

/**
 * @var $user User
 * @var $models User[]
 */

?>

<div class="contractors-index">

    <div class="row" style="margin-top: 35px; padding-left: 25px; padding-right: 25px;">

        <div class="col-md-9">
            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>

        <div class="col-md-3">
            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>
                <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Добавить исполнителя</div></div>',
                    ['/contractors/add'], ['class' => 'new_hypothesis_link_plus pull-right']
                ) ?>
            <?php endif; ?>
        </div>

    </div>

    <div class="row container-fluid contractor-headers">

        <div class="col-md-4 pl-30">
            Логин
        </div>

        <div class="col-md-1">
            Проекты *
        </div>

        <div class="col-md-5" style="display:flex; align-items: center; justify-content: space-between;">
            <div>Последняя коммуникация *</div>
            <div style="padding-right: 60px;">Дата</div>
        </div>

        <div class="col-md-2"></div>

    </div>

    <div class="row block_all_contractors">

        <?= $this->render('index_ajax', ['models' => $models, 'user' => $user]) ?>

    </div>

    <div class="information_header_table_container">
        <div class="information_header_table">
            <div><span class="bolder">Проекты *</span> - соотношение проектов текущего руководителя, в которых участвует исполнителей, и всех проектов, на которые был назначен исполнитель.</div>
            <div><span class="bolder">Последняя коммуникация *</span> - последняя коммуникация руководителя и исполнителя по проектам текущего руководителя.</div>
        </div>
    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/user_contractors.js'); ?>