<?php

use app\models\ContractorTaskSimilarProductParams;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var ContractorTaskSimilarProductParams[] $models
 * @var ContractorTaskSimilarProductParams $newModel
 * @var int $taskId
 */

?>

<div class="">

    <div class="row">
        <div class="col-md-12 pull-right">
            <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Добавить параметр</div></div>',
                ['#'], ['id' => 'showNewSimilarParamForm', 'class' => 'link_add_respond_text pull-right']
            ) ?>
        </div>
    </div>

    <div class="newSimilarParam">
        <?php $form = ActiveForm::begin([
            'id' => 'formCreateContractorTaskSimilarParam',
            'action' => Url::to(['/contractor/products/create-similar-param', 'taskId' => $taskId]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <div class="row">

            <div class="col-md-12">

                <?= $form->field($newModel, 'name', ['template' => '{input}'])->textInput([
                    'maxlength' => true,
                    'required' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => 'Напишите наименование параметра сравнения',
                    'autocomplete' => 'off'
                ]) ?>

            </div>

            <div class="form-group col-xs-12" style="display: flex; justify-content: center; margin-top: 15px;">

                <?= Html::submitButton('Сохранить', [
                    'id' => 'createSimilarParamSubmit',
                    'class' => 'btn btn-default pull-right',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'background' => '#7F9FC5',
                        'width' => '180px',
                        'height' => '40px',
                        'border-radius' => '8px',
                        'text-transform' => 'uppercase',
                        'font-size' => '16px',
                        'color' => '#FFFFFF',
                        'font-weight' => '700',
                    ]

                ]) ?>

            </div>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="mt-15">

        <?php if (count($models) > 0): ?>
            <?php foreach ($models as $model): ?>
                <div class="row viewSimilarParam viewSimilarParam-<?= $model->getId() ?>">

                    <?php if (!$model->getDeletedAt()): ?>

                        <div class="col-xs-10"><?= $model->getName() ?></div>
                        <div class="col-xs-2" style="display:flex; align-items: center; justify-content: right;">

                            <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                ['#'], ['id' => 'showUpdateSimilarParam-' . $model->getId(), 'class' => 'showUpdateSimilarParam', 'title' => 'Редактировать']) ?>

                            <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]), [
                                '/contractor/products/delete-similar-param', 'id' => $model->getId()], ['id' => 'deleteSimilarParam-' . $model->getId(), 'class' => 'deleteSimilarParam', 'title' => 'Удалить']) ?>

                        </div>

                    <?php else: ?>

                        <div class="col-xs-10 color-red"><?= $model->getName() ?></div>
                        <div class="col-xs-2" style="display:flex; align-items: center; justify-content: right;">

                            <?= Html::a(Html::img('/images/icons/recovery_icon.png', ['style' => ['width' => '24px']]),
                                ['/contractor/products/recovery-similar-param', 'id' => $model->getId()], [
                                'id' => 'recoverySimilarParam-' . $model->getId(), 'class' => 'recoverySimilarParam', 'title' => 'Восстановить']) ?>

                        </div>

                    <?php endif; ?>
                </div>

                <div class="row updateSimilarParam updateSimilarParam-<?= $model->getId() ?>">

                    <?php $form = ActiveForm::begin([
                        'id' => 'formUpdateContractorTaskSimilarParam-' . $model->getId(),
                        'action' => Url::to(['/contractor/products/update-similar-param', 'id' => $model->getId()]),
                        'options' => ['class' => 'g-py-15 formUpdateContractorTaskSimilarParam'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                    <div class="col-md-8">

                        <?= $form->field($model, 'name', ['template' => '{input}'])->textInput([
                            'maxlength' => true,
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Напишите наименование параметра сравнения',
                            'autocomplete' => 'off'
                        ]) ?>

                    </div>

                    <div class="form-group col-md-4" style="display: flex; justify-content: space-around; padding-top: 2px;">

                        <?= Html::button('Отмена', [
                            'id' => 'cancelUpdateSimilarParam-' . $model->getId(),
                            'class' => 'btn btn-default cancelUpdateSimilarParam',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'width' => '120px',
                                'height' => '40px',
                                'border-radius' => '8px',
                                'text-transform' => 'uppercase',
                                'font-size' => '16px',
                                'font-weight' => '700',
                            ]
                        ])?>

                        <?= Html::submitButton('Сохранить', [
                            'id' => 'updateSimilarParamSubmit-' . $model->getId(),
                            'class' => 'btn btn-default pull-right',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#7F9FC5',
                                'width' => '180px',
                                'height' => '40px',
                                'border-radius' => '8px',
                                'text-transform' => 'uppercase',
                                'font-size' => '16px',
                                'color' => '#FFFFFF',
                                'font-weight' => '700',
                            ]

                        ]) ?>

                    </div>

                    <?php ActiveForm::end(); ?>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>

        <div class="row" style="display: flex; align-items: center; min-height: 60px; border-top: 1px solid #cccccc;">
            <div class="col-md-12">Стоимость владения</div>
        </div>

        <div class="row" style="display: flex; align-items: center; min-height: 60px; border-top: 1px solid #cccccc; border-bottom: 1px solid #cccccc;">
            <div class="col-md-12">Цена</div>
        </div>

    </div>
</div>
