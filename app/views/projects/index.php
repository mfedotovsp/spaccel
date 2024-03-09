<?php

use app\models\Authors;
use app\models\forms\SearchForm;
use app\models\Projects;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use yii\widgets\ActiveForm;

/**
 * @var User $user
 * @var Projects[] $models
 * @var Authors $new_author
 * @var SearchForm $searchForm
 * @var bool $existTrashList
 * @var Projects[] $trashList
*/

$this->title = 'Проекты';
$this->registerCssFile('@web/css/projects-index-style.css');

?>
<div class="projects-index">

    <div class="row">
        <div class="col-xs-12 header-title-mobile"><?= $this->title ?></div>
    </div>

    <?php if (!User::isUserExpert(Yii::$app->user->identity['username']) && !User::isUserContractor(Yii::$app->user->identity['username'])) : ?>

        <div class="row project_menu">

            <?= Html::a('Проекты', ['/projects/index', 'id' => $user->getId()], [
                'class' => 'link_in_the_header',
            ]) ?>

            <?= Html::a('Сводные таблицы', ['/projects/results', 'id' => $user->getId()], [
                'class' => 'link_in_the_header',
            ]) ?>

            <?= Html::a('Трэкшн карты', ['/projects/roadmaps', 'id' => $user->getId()], [
                'class' => 'link_in_the_header',
            ]) ?>

            <?= Html::a('Протоколы', ['/projects/reports', 'id' => $user->getId()], [
                'class' => 'link_in_the_header',
            ]) ?>

            <?= Html::a('Презентации', ['/projects/presentations', 'id' => $user->getId()], [
                'class' => 'link_in_the_header',
            ]) ?>

        </div>

    <?php endif; ?>

    <div class="container-fluid container-data row">

        <div class="row row_header_data_generation" style="margin-top: 10px;">

            <div class="col-md-3" style="padding: 2px 0;">
                <?= Html::a('Проекты' . Html::img('/images/icons/icon_report_next.png'), ['/projects/get-instruction'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

            <?php if (!User::isUserExpert(Yii::$app->user->identity['username']) && !User::isUserContractor(Yii::$app->user->identity['username'])) : ?>

                <?php if ($existTrashList): ?>

                    <div class="col-md-4 search_block_desktop">

                        <?php
                        $form = ActiveForm::begin([
                            'id' => 'search_projects',
                            'options' => ['class' => 'g-py-15'],
                            'errorCssClass' => 'u-has-error-v1',
                            'successCssClass' => 'u-has-success-v1-1',
                        ]); ?>

                        <?= $form->field($searchForm, 'search', ['template' => '{input}'])
                            ->textInput([
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'поиск проекта',
                                'minlength' => 5,
                                'autocomplete' => 'off'])
                            ->label(false) ?>

                        <?php ActiveForm::end(); ?>

                    </div>

                    <div class="col-md-3" style="padding: 0;">
                        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                            <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый проект</div></div>', ['/projects/get-hypothesis-to-create', 'id' => $user->getId()],
                                ['id' => 'showHypothesisToCreate', 'class' => 'new_hypothesis_link_plus pull-right']
                            ) ?>

                        <?php endif; ?>
                    </div>

                    <div class="col-md-2 p-0">
                        <?=  Html::a( '<div class="hypothesis_trash_link_block"><div>' . Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div><div style="padding-left: 20px;">Корзина</div></div>',
                            ['/projects/trash-list', 'id' => $user->getId()],
                            ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right']
                        ) ?>
                    </div>

                <?php else : ?>

                    <div class="col-md-6 search_block_desktop">

                        <?php
                        $form = ActiveForm::begin([
                            'id' => 'search_projects',
                            'options' => ['class' => 'g-py-15'],
                            'errorCssClass' => 'u-has-error-v1',
                            'successCssClass' => 'u-has-success-v1-1',
                        ]); ?>

                        <?= $form->field($searchForm, 'search', ['template' => '{input}'])
                            ->textInput([
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'поиск проекта',
                                'minlength' => 5,
                                'autocomplete' => 'off'])
                            ->label(false) ?>

                        <?php ActiveForm::end(); ?>

                    </div>

                    <div class="col-md-3" style="padding: 0;">
                        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                            <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый проект</div></div>', ['/projects/get-hypothesis-to-create', 'id' => $user->getId()],
                                ['id' => 'showHypothesisToCreate', 'class' => 'new_hypothesis_link_plus pull-right']
                            ) ?>

                        <?php endif; ?>
                    </div>

                <?php endif; ?>

            <?php else : ?>

                <div class="col-md-6"></div>
                <div class="col-md-3 p-0"></div>

            <?php endif; ?>

        </div>

        <!--Заголовки для списка проектов-->
        <div class="row headers_for_list_projects">

            <div class="col-lg-3 header_data_hypothesis">
                <div class="">Проект</div>
            </div>

            <div class="col-lg-3 header_data_hypothesis" style="padding-left: 10px;">
                Результат интеллектуальной деятельности
            </div>

            <div class="col-lg-2 header_data_hypothesis">
                Базовая технология
            </div>

            <div class="col-lg-4 header_data_hypothesis">
                Создан / Изменен
            </div>

        </div>

        <div class="row row_header_data_generation_mobile">

            <div class="col-xs-7">
                <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                    <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый проект</div></div>', ['/projects/get-hypothesis-to-create', 'id' => $user->getId()],
                        ['id' => 'showHypothesisToCreate', 'class' => 'new_hypothesis_link_plus']
                    ) ?>

                <?php endif; ?>
            </div>

            <div class="col-xs-5">

                <?php if (!User::isUserExpert(Yii::$app->user->identity['username']) && !User::isUserContractor(Yii::$app->user->identity['username'])) : ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                        Url::to('/projects/get-instruction'), [
                            'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                            'title' => 'Инструкция', 'style' => ['margin-left' => '10px', 'margin-top' => '5px']
                        ]) ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_green_search.png'), ['#'], [
                            'class' => 'link_show_search_field_mobile show_search_projects pull-right',
                            'title' => 'Поиск проектов', 'style' => ['margin-top' => '5px']
                    ]) ?>

                    <?php if ($existTrashList): ?>
                        <?=  Html::a('<div class="hypothesis_trash_link_block"><div>' .  Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div></div>',
                            ['/projects/trash-list', 'id' => $user->getId()], ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right', 'title' => 'Корзина']
                        ) ?>
                    <?php endif; ?>

                <?php else : ?>

                    <?php if (!$models[0]->getDeletedAt()): ?>

                        <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                            Url::to('/projects/get-instruction'), [
                                'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                                'title' => 'Инструкция', 'style' => ['margin-top' => '5px']
                            ]) ?>

                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>

        <div class="row search_block_mobile">
            <div class="col-xs-10">
                <?php $form = ActiveForm::begin([
                    'id' => 'search_projects_mobile',
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <?= $form->field($searchForm, 'search', ['template' => '{input}'])
                    ->textInput([
                        'class' => 'style_form_field_respond form-control',
                        'placeholder' => 'поиск проекта',
                        'minlength' => 5,
                        'autocomplete' => 'off'])
                    ->label(false) ?>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-xs-2 pull-right">
                <?= Html::a(Html::img('@web/images/icons/cancel_danger.png'), ['#'], ['class' => 'link_cancel_search_field_mobile show_search_projects']) ?>
            </div>
        </div>

        <div class="block_all_projects_user">

            <!--Данные для списка проектов-->
            <?= $this->render('_index_ajax', ['models' => $models]) ?>
        </div>
    </div>

    <div class="form_authors" style="display: none;">

        <?php
        $form = ActiveForm::begin([
            'id' => 'form_authors'
        ]); ?>

        <div class="form_authors_inputs">

            <div class="row row-author row-author-" style="margin-bottom: 15px;">

                <?= $form->field($new_author, "[0]fio", [
                    'template' => '<div class="col-md-12" style="padding-left: 20px; margin-top: 15px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'required' => true,
                    'id' => 'author_fio-',
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>

                <?= $form->field($new_author, "[0]role", [
                    'template' => '<div class="col-md-12" style="padding-left: 20px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'required' => true,
                    'id' => 'author_role-',
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>

                <?= $form->field($new_author, "[0]experience", [
                    'template' => '<div class="col-md-12" style="padding-left: 20px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textarea([
                    'rows' => 2,
                    'id' => 'author_experience-',
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

                <div class="col-md-12">

                    <?= Html::button('Удалить автора', [
                        'id' => 'remove-author-',
                        'class' => "remove-author btn btn-default",
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'background' => '#707F99',
                            'color' => '#FFFFFF',
                            'width' => '200px',
                            'height' => '40px',
                            'font-size' => '16px',
                            'text-transform' => 'uppercase',
                            'font-weight' => '700',
                            'padding-top' => '9px',
                            'border-radius' => '8px',
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
    </div>

    <!--Модальные окна-->
    <?= $this->render('modal') ?>

</div>


<!--Подключение скриптов-->
<?php
$this->registerJsFile('@web/js/project_index.js');
$this->registerJsFile('@web/js/main_expertise.js');
?>