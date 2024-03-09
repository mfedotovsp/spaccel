<?php

use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Projects;
use app\models\Authors;

/**
 * @var Projects $model
 * @var Authors[] $workers
 */

?>

<div class="form-update-project">

    <?php $form = ActiveForm::begin([
        'id' => 'project_update_form',
        'action' => Url::to(['projects/update', 'id' => $model->getId()]),
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'project_name', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-5">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'project_fullname', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'description', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'purpose_project', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Примеры: разработать продукт, найти целевой сегмент, найти рекламный слоган, разработать упаковку и т.д.',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'rid', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'core_rid', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'patent_name', [
            'template' => '<div class="col-md-12  pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'patent_number', [
            'template' => '<div class="col-md-12  pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-3">
            <?= '<label class="control-label pl-5">Дата получения патента</label>' ?>
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[patent_date]',
                'value' => empty($model->patent_date) ? null : date('d.m.Y', $model->patent_date),
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy'
                ],
                'options' => [
                    'id' => "patent_date-" . $model->getId(),
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ],
                'pluginEvents' => [
                    "hide" => "function(e) {e.preventDefault(); e.stopPropagation();}",
                ],
            ]) ?>
        </div>
    </div>

    <div class="section_title">Авторы</div>

    <div class="row">
        <div class="panel-body col-md-12">
            <div class="container-authors"><!-- widgetContainer -->
                <div class="item-authors item-authors-<?=$model->getId() ?> panel-body" style="padding: 0;"><!-- widgetBody -->

                    <?php foreach ($workers as $i => $worker): ?>

                        <div class="row row-author row-author-<?= $model->getId() . '_' . $i ?>" style="margin-bottom: 15px;">

                            <?= $form->field($worker, "[$i]fio", [
                                'template' => '<div class="col-md-12" style="padding-left: 20px; margin-top: 15px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                            ])->textInput([
                                'maxlength' => true,
                                'required' => true,
                                'id' => 'author_fio-' . $i,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => '',
                                'autocomplete' => 'off'
                            ]) ?>

                            <?= $form->field($worker, "[$i]role", [
                                'template' => '<div class="col-md-12" style="padding-left: 20px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                            ])->textInput([
                                'maxlength' => true,
                                'required' => true,
                                'id' => 'author_role-' . $i,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => '',
                                'autocomplete' => 'off'
                            ]) ?>

                            <?= $form->field($worker, "[$i]experience", [
                                'template' => '<div class="col-md-12" style="padding-left: 20px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                            ])->textarea([
                                'rows' => 2,
                                'maxlength' => true,
                                'id' => 'author_experience-' . $i,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => '',
                            ]) ?>

                            <?php if ($i !== 0) : ?>

                                <div class="col-md-12">

                                    <?= Html::button('Удалить автора', [
                                        'id' => 'remove-author-' . $model->getId() . '_' . $i . '-' . $worker->getId(),
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

                            <?php endif; ?>

                        </div><!-- .row -->

                    <?php endforeach; ?>

                </div>
            </div>

            <?= Html::button('Добавить автора', [
                'id' => 'add_author-' . $model->getId(),
                'class' => "btn btn-default add_author",
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'color' => '#FFFFFF',
                    'justify-content' => 'center',
                    'background' => '#52BE7F',
                    'width' => '200px',
                    'height' => '40px',
                    'text-align' => 'left',
                    'font-size' => '16px',
                    'text-transform' => 'uppercase',
                    'font-weight' => '700',
                    'padding-top' => '9px',
                    'border-radius' => '8px',
                    'margin-right' => '5px',
                ]
            ]) ?>

        </div>
    </div>

    <div class="row desktop-mb-15 mt-30">
        <?= $form->field($model, 'technology', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'layout_technology', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'register_name', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-3">

            <?= '<label class="control-label pl-5">Дата регистрации</label>' ?>
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[register_date]',
                'value' => empty($model->register_date) ? null : date('d.m.Y', $model->register_date),
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'id' => "register_date-" . $model->getId(),
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ],
                'pluginEvents' => [
                    "hide" => "function(e) {e.preventDefault(); e.stopPropagation();}",
                ],
            ]) ?>

        </div>
    </div>

    <div class="row desktop-mb-15 mobile-mt-15">
        <?= $form->field($model, 'site', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'invest_name', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <script>
        $( function() {
            var invest_amount = 'input#invest_amount-<?= $model->id ?>';
            $(invest_amount).change(function () {
                var value = $(invest_amount).val();
                var valueMax = 100000000;
                var valueMin = 50000;
                if (parseInt(value) > parseInt(valueMax)){
                    value = valueMax;
                    $(invest_amount).val(value);
                }
                if (parseInt(value) < parseInt(valueMin)){
                    value = valueMin;
                    $(invest_amount).val(value);
                }
            });
        } );
    </script>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'invest_amount', [
            'template' => '<div class="col-md-12" style="padding-top: 7px; padding-left: 20px;">{label}<div style="font-weight: 400;font-size: 13px; margin-top: -5px; margin-bottom: 5px;">(укажите значение от 50 000 до 100 млн.)</div></div><div class="col-md-3">{input}</div><div class="col-md-5"></div>'
        ])->textInput([
            'type' => 'number',
            'id' => 'invest_amount-' . $model->getId(),
            'class' => 'style_form_field_respond form-control',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-12">
            <?= '<label class="control-label pl-5">Дата получения инвестиций</label>' ?>
        </div>
        <div class="col-md-3">

            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[invest_date]',
                'value' => empty($model->invest_date) ? null : date('d.m.Y', $model->invest_date),
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'id' => "invest_date-" . $model->getId(),
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ],
                'pluginEvents' => [
                    "hide" => "function(e) {e.preventDefault(); e.stopPropagation();}",
                ],
            ]) ?>

        </div>
        <div class="col-md-5"></div>
    </div>

    <div class="row desktop-mb-15 mobile-mt-15">
        <?= $form->field($model, 'announcement_event', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-12">
            <?= '<label class="control-label pl-5">Дата анонсирования проекта</label>' ?>
        </div>
        <div class="col-md-3">

            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[date_of_announcement]',
                'value' => empty($model->date_of_announcement) ? null : date('d.m.Y', $model->date_of_announcement),
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'id' => "date_of_announcement-$model->id",
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ],
                'pluginEvents' => [
                    "hide" => "function(e) {e.preventDefault(); e.stopPropagation();}",
                ],
            ]) ?>

        </div>
        <div class="col-md-5"></div>
    </div>

    <div class="container row mobile-mt-20">
        <div class="pull-left">

            <?php if (count($model->preFiles) < 5) : ?>

                <div class="add_files">

                    <div style="margin-top: -5px; padding-left: 5px;">
                        <label>Презентационные файлы</label>
                        <p style="margin-top: -5px; color: #BDBDBD;">
                            (максимум 5 файлов - png, jpg, jpeg, pdf, txt, doc, docx, xls)
                        </p>
                    </div>

                    <div class="error_files_count text-danger" style="display: none; margin-top: -5px; padding-left: 5px;">
                        Превышено максимальное количество файлов для загрузки.
                    </div>

                    <div style="padding-left: 5px;"><?= $form->field($model, 'present_files[]', ['template' => "{label}\n{input}"])->fileInput(['multiple' => true])->label(false) ?></div>

                </div>

                <div class="add_max_files_text" style="display: none; margin-top: -5px; padding-left: 5px;">
                    <label>Добавлено максимальное количество файлов.</label>
                    <p style="margin-top: -5px; color: #BDBDBD;">Чтобы загрузить новые файлы, удалите уже загруженные.</p>
                </div>

            <?php else : ?>

                <div class="add_files" style="display: none;">

                    <div style="margin-top: -5px; padding-left: 5px;">
                        <label>Презентационные файлы</label>
                        <p style="margin-top: -5px; color: #BDBDBD;">
                            (максимум 5 файлов - png, jpg, jpeg, pdf, txt, doc, docx, xls)
                        </p>
                    </div>

                    <div class="error_files_count text-danger" style="display: none; margin-top: -5px; padding-left: 5px;">
                        Превышено максимальное количество файлов для загрузки.
                    </div>

                    <div style="padding-left: 5px;"><?= $form->field($model, 'present_files[]', ['template' => "{label}\n{input}"])->fileInput(['multiple' => true])->label(false) ?></div>

                </div>

                <div class="add_max_files_text" style="margin-top: -5px; padding-left: 5px;">
                    <label>Добавлено максимальное количество файлов.</label>
                    <p style="margin-top: -5px; color: #BDBDBD;">Чтобы загрузить новые файлы, удалите уже загруженные.</p>
                </div>

            <?php endif; ?>

            <div class="block_all_files" style="padding-left: 5px;">
                <?php if (!empty($model->preFiles)){
                    foreach ($model->preFiles as $file){
                        $filename = $file->getFileName();
                        if(mb_strlen($filename) > 35){ $filename = mb_substr($file->getFileName(), 0, 35) . '...'; }
                        echo '<div style="display: flex; margin: 2px 0; align-items: center;" class="one_block_file-'.$file->id.'">' .
                            Html::a('<div style="display:flex; width: 100%; justify-content: space-between;"><div>' . $filename . '</div><div>'. Html::img('/images/icons/icon_export.png', ['style' => ['width' => '22px']]) .'</div></div>', ['download', 'id' => $file->getId()], [
                                'title' => 'Скачать файл',
                                'target' => '_blank',
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
                            ]) . ' ' .
                            Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px', 'height' => '29px']]), ['delete-file', 'id' => $file->getId()], [
                                'title' => 'Удалить файл',
                                'class' => 'delete_file',
                                'id' => 'delete_file-' . $file->getId(),
                                'style' => ['display' => 'flex', 'margin-left' => '15px'],
                            ])
                            . '</div>';
                    }
                }?>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12" style="display:flex;justify-content: center;">
            <?= Html::submitButton('Сохранить', [
                'id' => 'save_update_form',
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#7F9FC5',
                    'color' => '#ffffff',
                    'width' => '180px',
                    'height' => '40px',
                    'font-size' => '16px',
                    'text-transform' => 'uppercase',
                    'font-weight' => '700',
                    'padding-top' => '9px',
                    'border-radius' => '8px',
                    'margin-top' => '28px'
                ]
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
