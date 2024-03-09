<?php

use app\models\WishList;
use yii\helpers\Html;

$this->title = 'Новые списки запросов B2B компаний';
$this->registerCssFile('@web/css/wish-list-style.css');

/**
 * @var WishList[] $models
 */

?>

<div class="container-fluid">
    <div class="row hi-line-page">
        <div class="col-md-6" style="margin-top: 35px; padding-left: 25px;">
            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>
        <div class="col-md-3 pull-right">
            <?=  Html::a( 'Готовые списки', ['/client/wish-list/index'], [
                    'class' => 'btn btn-success pull-right',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'color' => '#FFFFFF',
                        'justify-content' => 'center',
                        'background' => '#52BE7F',
                        'width' => '200px',
                        'height' => '40px',
                        'font-size' => '24px',
                        'border-radius' => '8px',
                        'margin-top' => '35px',
                    ]
                ]
            ) ?>
        </div>
        <div class="col-md-3" style="margin-top: 30px;">
            <?=  Html::a( '<div class="new_client_request_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый список</div></div>',
                ['/client/wish-list/create'], ['class' => 'new_client_request_link_plus pull-right']
            ) ?>
        </div>
    </div>
</div>

<div class="container-fluid">

    <div class="row headers_wish_lists_new">

        <div class="col-md-4">
            Наименование предприятия
        </div>

        <div class="col-md-3">
            Тип предприятия
        </div>

        <div class="col-md-3">
            Тип производства
        </div>

        <div class="col-md-2">

        </div>

    </div>

    <div class="block_all_wish_lists_new">

        <?= $this->render('new_ajax', ['models' => $models]) ?>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/wish_list_new.js'); ?>