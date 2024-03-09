<?php

use app\models\forms\FormFilterRequirement;
use app\models\LocationWishList;
use app\models\RequirementWishList;
use app\models\SizesWishList;
use app\models\TypesCompanyWishList;
use app\models\TypesProductionWishList;
use kartik\select2\Select2;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

/**
 * @var RequirementWishList[] $requirements
 * @var int $projectId
 * @var FormFilterRequirement $filters
 * @var Pagination $pages
 */

?>

<?php $form = ActiveForm::begin([
    'id' => 'filtersRequirement',
    'action' => Url::to(['/segments/get-list-requirements', 'projectId' => $projectId]),
    'options' => ['class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]); ?>

<div class="row container-fluid addFiltersForListRequirements mb-5">
    <div class="col-md-4">
        <?= Html::button('Добавить фильтры', [
            'id' => 'addFiltersForListRequirements',
            'class' => 'btn btn-success',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'color' => '#FFFFFF',
                'background' => '#52BE7F',
                'width' => '100%',
                'height' => '40px',
                'font-size' => '16px',
                'text-transform' => 'uppercase',
                'font-weight' => '700',
                'padding-top' => '9px',
                'border-radius' => '8px',
                'margin-bottom' => '5px',
            ]
        ]) ?>
    </div>
    <div class="col-md-8"></div>
</div>

<div class="row container-fluid buttonsFiltersForListRequirements mb-5">
    <div class="col-md-4">
        <?= Html::a('Сбросить фильтры', Url::to(['/segments/get-list-requirements', 'projectId' => $projectId]), [
            'id' => 'resetFiltersForListRequirements',
            'class' => 'btn btn-danger',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'color' => '#FFFFFF',
                'background' => '#FF5C5C',
                'width' => '100%',
                'height' => '40px',
                'font-size' => '16px',
                'text-transform' => 'uppercase',
                'font-weight' => '700',
                'padding-top' => '9px',
                'border-radius' => '8px',
                'margin-bottom' => '5px',
            ]
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= Html::submitButton('Применить фильтры', [
            'class' => 'btn btn-success',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'color' => '#FFFFFF',
                'background' => '#52BE7F',
                'width' => '100%',
                'height' => '40px',
                'font-size' => '16px',
                'text-transform' => 'uppercase',
                'font-weight' => '700',
                'padding-top' => '9px',
                'border-radius' => '8px',
                'margin-bottom' => '5px',
            ]
        ]) ?>
    </div>
    <div class="col-md-4"></div>
</div>

<div class="row container-fluid requirement-filters mb-5">

    <div class="col-md-12">
        <?= $form->field($filters, 'requirement', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'reason', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'expectedResult', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'fieldOfActivity', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'sortOfActivity', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'size', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
        ])->widget(Select2::class, [
            'data' => SizesWishList::getList(),
            'options' => ['placeholder' => ''],
            'disabled' => false,  //Сделать поле неактивным
            'hideSearch' => true, //Скрытие поиска
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'locationId', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
        ])->widget(Select2::class, [
            'data' => LocationWishList::getList(),
            'options' => ['placeholder' => ''],
            'disabled' => false,  //Сделать поле неактивным
            'hideSearch' => false, //Скрытие поиска
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'typeCompany', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
        ])->widget(Select2::class, [
            'data' => TypesCompanyWishList::getList(),
            'options' => ['placeholder' => ''],
            'disabled' => false,  //Сделать поле неактивным
            'hideSearch' => true, //Скрытие поиска
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($filters, 'typeProduction', [
            'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
        ])->widget(Select2::class, [
            'data' => TypesProductionWishList::getList(),
            'options' => ['placeholder' => ''],
            'disabled' => false,  //Сделать поле неактивным
            'hideSearch' => true, //Скрытие поиска
        ]) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>

<div class="row headers-list-requirements">
    <div class="col-md-4">Запрос</div>
    <div class="col-md-4">Причины</div>
    <div class="col-md-4">Ожидаемое решение</div>
</div>

<?php if (count($requirements) > 0): ?>

    <?php foreach ($requirements as $requirement): ?>

        <?php $wishList = $requirement->wishList ?>

        <div class="row container-one_requirement">
            <div class="col-md-4">
                <div class="header-column-requirement-mobile">Запрос:</div>
                <div><?= $requirement->getRequirement() ?></div>
            </div>
            <div class="col-md-4">
                <div class="header-column-requirement-mobile">Причины:</div>
                <?php foreach ($requirement->reasons as $reason): ?>
                    <div>- <?= $reason->getReason() ?></div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-4">
                <div class="header-column-requirement-mobile">Ожидаемое решение:</div>
                <?= $requirement->getExpectedResult() ?>
            </div>
            <div class="col-md-12">
                <div class="row details-requirement">
                    <?php if ($requirement->getAddInfo() !== ''): ?>
                        <div class="col-md-12">
                            <span class="bolder">Дополнительная информация о запросе:</span>
                            <span><?= $requirement->getAddInfo() ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-12">
                        <span class="bolder">Наименование предприятия:</span>
                        <span><?= $wishList->getCompanyName() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span class="bolder">Тип предприятия:</span>
                        <span><?= $wishList->getTypeCompanyName() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span class="bolder">Тип производства:</span>
                        <span><?= $wishList->getTypeProductionName() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span class="bolder">Размер предприятия по количеству персонала:</span>
                        <span><?= $wishList->getSizeName() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span class="bolder">Локация предприятия:</span>
                        <span><?= $wishList->location->getName() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span class="bolder">Сфера деятельности предприятия:</span>
                        <span><?= $wishList->getCompanyFieldOfActivity() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span class="bolder">Вид деятельности предприятия:</span>
                        <span><?= $wishList->getCompanySortOfActivity() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span class="bolder">Продукция/услуги предприятия:</span>
                        <span><?= $wishList->getCompanyProducts() ?></span>
                    </div>
                    <?php if ($wishList->getAddInfo() !== ''): ?>
                        <div class="col-md-12">
                            <span class="bolder">Дополнительная информация о предприятии:</span>
                            <span><?= $wishList->getAddInfo() ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-12">
                        <?= Html::a('Выбрать запрос', ['/segments/get-hypothesis-to-create', 'id' => $projectId, 'useWishList' => true, 'requirementId' => $requirement->getId()], [
                            'class' => 'btn btn-default select-requirement',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'color' => '#FFFFFF',
                                'background' => '#52BE7F',
                                'width' => '150px',
                                'height' => '40px',
                                'font-size' => '16px',
                                'font-weight' => '700',
                                'padding-top' => '9px',
                                'border-radius' => '8px',
                                'text-transform' => 'uppercase',
                                'margin-top' => '10px',
                                'margin-bottom' => '10px',
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="row container-fluid mt-15">
        <div class="col-md-12 text-center bolder">Отсутствуют данные</div>
    </div>

<?php endif; ?>

<div class="row container-fluid pagination-admin-projects-result">
    <?= LinkPager::widget([
        'pagination' => $pages,
        'activePageCssClass' => 'pagination_active_page',
        'options' => ['class' => 'admin-projects-result-pagin-list'],
    ]) ?>
</div>
