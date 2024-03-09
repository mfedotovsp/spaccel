<?php

use app\models\ContractorTaskProducts;
use app\models\ContractorTasks;
use app\models\StageExpertise;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var ContractorTaskProducts[] $models
 * @var bool $allowEdit
 * @var bool $isMobile
 */

?>

<div class="product-list">

    <?php if (count($models) < 1): ?>

        <div class="text-center mt-15 font-size-18">Отсутствуют добавленные продукты</div>

    <?php else: ?>

        <?php foreach ($models as $key => $model): ?>

            <?php if (!$isMobile): ?>

                <div class="row container-one_product" id="taskProduct-<?= $model->getId() ?>" style="margin: 3px 0; padding: 0;">
                    <div class="col-md-11" title="Смотреть описание продукта">
                        <span class="bolder"><?= ($key+1) ?>.</span>
                        <?= $model->getName() ?>
                    </div>
                    <div class="col-md-1" style="text-align: right;">

                        <?php if ($allowEdit) {

                            echo Html::a(Html::img('/images/icons/update_warning_vector.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                Url::to(['/contractor/products/get-data-update-form', 'id' => $model->getId()]), [
                                    'id' => 'taskProduct_form-' . $model->getId(),
                                    'class' => 'showTaskProductUpdateForm',
                                    'title' => 'Редактировать описание продукта',
                                ]);

                            echo Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
                                Url::to(['/contractor/products/delete', 'id' => $model->getId()]), [
                                    'id' => 'link_task_product_delete-' . $model->getId(),
                                    'class' => 'showDeleteTaskProductModal',
                                    'title' => 'Удалить продукт',
                                ]);
                        } ?>

                    </div>
                </div>

                <div class="row container-one_product_desc taskProduct-<?= $model->getId() ?>">

                    <div class="col-md-12">
                        <span class="bolder">Цена продукта:</span>
                        <span><?= $model->getPrice() ?> руб.</span>
                    </div>

                    <div class="col-md-12">
                        <span class="bolder">Удовлетворенность продуктом:</span>
                        <span><?= $model->getTitleSatisfaction() ?></span>
                    </div>

                    <div class="col-md-12">
                        <div class="bolder">Недостатки продукта:</div>
                        <div><?= $model->getFlaws() ?></div>
                    </div>

                    <div class="col-md-12">
                        <div class="bolder">Преимущества продукта:</div>
                        <div><?= $model->getAdvantages() ?></div>
                    </div>

                    <div class="col-md-12">
                        <div class="bolder">Ключевые поставщики:</div>
                        <div><?= $model->getSuppliers() ?></div>
                    </div>

                </div>

            <?php else: ?>

                <div class="hypothesis_table_mobile" style="margin-bottom: 5px;">
                    <div class="row container-one_hypothesis_mobile row_hypothesis-<?= $model->getId() ?>">

                        <div class="col-md-12 font-size-18 mb-10">
                            <div class="bolder">Наименование продукта:</div>
                            <div><?= $model->getName() ?></div>
                        </div>

                        <div class="col-md-12">
                            <span class="bolder">Цена продукта:</span>
                            <span><?= $model->getPrice() ?> руб.</span>
                        </div>

                        <div class="col-md-12">
                            <span class="bolder">Удовлетворенность продуктом:</span>
                            <span><?= $model->getTitleSatisfaction() ?></span>
                        </div>

                        <div class="col-md-12">
                            <div class="bolder">Недостатки продукта:</div>
                            <div><?= $model->getFlaws() ?></div>
                        </div>

                        <div class="col-md-12">
                            <div class="bolder">Преимущества продукта:</div>
                            <div><?= $model->getAdvantages() ?></div>
                        </div>

                        <div class="col-md-12">
                            <div class="bolder">Ключевые поставщики:</div>
                            <div><?= $model->getSuppliers() ?></div>
                        </div>

                        <div class="hypothesis_buttons_mobile">

                            <?php if ($allowEdit): ?>

                                <?= Html::a('Редактировать',
                                    Url::to(['/contractor/products/get-data-update-form', 'id' => $model->getId()]), [
                                        'id' => 'taskProduct_form-' . $model->getId(),
                                        'class' => 'btn btn-default showTaskProductUpdateForm',
                                        'style' => [
                                            'display' => 'flex',
                                            'width' => '47%',
                                            'height' => '36px',
                                            'background' => '#7F9FC5',
                                            'color' => '#FFFFFF',
                                            'align-items' => 'center',
                                            'justify-content' => 'center',
                                            'border-radius' => '0',
                                            'border' => '1px solid #ffffff',
                                            'font-size' => '18px',
                                            'margin' => '10px 1% 0% 2%',
                                        ],
                                    ]) ?>

                                <?= Html::a('Удалить продукт',
                                    Url::to(['/contractor/products/delete', 'id' => $model->getId()]), [
                                        'id' => 'link_task_product_delete-' . $model->getId(),
                                        'class' => 'btn btn-default showDeleteTaskProductModal',
                                        'style' => [
                                            'display' => 'flex',
                                            'width' => '47%',
                                            'height' => '36px',
                                            'background' => '#F5A4A4',
                                            'color' => '#FFFFFF',
                                            'align-items' => 'center',
                                            'justify-content' => 'center',
                                            'border-radius' => '0',
                                            'border' => '1px solid #ffffff',
                                            'font-size' => '18px',
                                            'margin' => '10px 2% 0% 1%',
                                        ],
                                    ]) ?>

                            <?php endif; ?>

                        </div>

                    </div>
                </div>

            <?php endif; ?>

        <?php endforeach; ?>

        <?php $task = $models[0]->task; ?>

        <?php if ($isMobile || $task->getType() === StageExpertise::CONFIRM_SEGMENT): ?>

            <div class="block_all_files pl-5 mb-15">

                <?php if (!empty($task->files)): ?>

                    <label class="mt-30">Приложенные файлы:</label>

                    <?php foreach ($task->files as $file){
                        $filename = $file->getFileName();
                        if(mb_strlen($filename) > 35){ $filename = mb_substr($file->getFileName(), 0, 35) . '...'; }
                        echo '<div style="display: flex; margin: 2px 0; align-items: center;" class="one_block_file-'.$file->getId().'">' .
                            Html::a('<div style="display:flex; width: 100%; justify-content: space-between;"><div>' . $filename . '</div><div>'. Html::img('/images/icons/icon_export.png', ['style' => ['width' => '22px']]) .'</div></div>', ['/contractor/tasks/download', 'id' => $file->getId()], [
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
                            ]) . ' ';

                        if (User::isUserContractor(Yii::$app->user->identity['username']) && $task->getStatus() === ContractorTasks::TASK_STATUS_RETURNED) {

                            echo Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px', 'height' => '29px']]), ['/contractor/tasks/delete-file', 'id' => $file->getId()], [
                                'title' => 'Удалить файл',
                                'class' => 'delete_file',
                                'id' => 'delete_file-' . $file->getId(),
                                'style' => ['display' => 'flex', 'margin-left' => '15px'],
                            ]);
                        }

                        echo '</div>';
                    }?>

                <?php endif; ?>
            </div>

        <?php endif; ?>

        <?php
        $formTaskComplete = new FormTaskComplete();
        if ($allowEdit && ($isMobile || $task->getType() === StageExpertise::CONFIRM_SEGMENT)): ?>

            <div class="mt-15 buttonShowTaskCompleteForm" style="display: flex; justify-content: center;">

                <?= Html::button('Завершить задание',[
                    'class' => 'btn btn-default showTaskFormComplete',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'width' => '220px',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                        'margin-right' => '10px',
                        'background' => '#4F4F4F',
                        'color' => '#FFFFFF',
                    ]
                ]) ?>

            </div>

            <div class="mt-15 blockTaskCompleteForm">

                <?php $form = ActiveForm::begin([
                    'action' => Url::to(['/contractor/tasks/complete', 'id' => $task->getId()]),
                    'id' => 'completeTaskForm',
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <div class="row" style="margin-bottom: 15px;">
                    <?= $form->field($formTaskComplete, 'comment', [
                        'template' => '<div class="col-md-3"></div><div class="col-md-6">{input}</div><div class="col-md-3"></div>'
                    ])->textarea([
                        'rows' => 1,
                        'required' => true,
                        'maxlength' => true,
                        'class' => 'style_form_field_respond form-control',
                        'placeholder' => 'Напишите комментарий',
                        'autocomplete' => 'off'
                    ])->label(false) ?>
                </div>

                <div class="row mobile-mt-20">
                    <div class="">
                        <div class="add_files">
                            <div class="pl-5 text-center mt--5">
                                <label class="">Прикрепить файлы</label>
                                <span  class="pl-5 mt--5" style="color: #BDBDBD;">
                                    (максимум 5 файлов - png, jpg, jpeg, pdf, txt, doc, docx, xls)
                                </span>
                            </div>
                            <div class="error_files_count text-danger display-none pl-5 text-center mt--5">
                                Превышено максимальное количество файлов для загрузки.
                            </div>
                            <div class="pl-5 display-flex justify-content-center ml-50">
                                <?= $form->field($formTaskComplete, 'files[]', ['template' => "{label}\n{input}"])->fileInput(['multiple' => true])->label(false) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-15" style="display: flex; justify-content: center;">

                    <?= Html::button('Отмена', [
                        'class' => 'btn btn-lg hiddenTaskFormComplete',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'width' => '150px',
                            'height' => '40px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                            'margin-right' => '10px',
                            'background' => '#4F4F4F',
                            'color' => '#FFFFFF',
                        ],
                    ]) ?>

                    <?= Html::submitButton('Завершить задание', [
                        'id' => 'submitTaskComplete',
                        'class' => 'btn btn-lg btn-success',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'background' => '#52BE7F',
                            'width' => '220px',
                            'height' => '40px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                        ],
                    ]) ?>

                </div>

                <?php ActiveForm::end(); ?>
            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>
