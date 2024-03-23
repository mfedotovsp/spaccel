<?php

use app\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;

/**
 * @var User $user
 * @var User[] $admins
 */

?>

<style>
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 16px;
        height: 40px;
        padding-left: 15px;
        padding-top: 8px;
        padding-bottom: 15px;
        border-radius: 12px;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 39px;
    }
</style>


<?php if ($user->getStatus() === User::STATUS_DELETED) : ?>

    <h4 class="row text-center">Невозможно изменить трекера заблокированному пользователю.</h4>

<?php else : ?>

    <?php $form = ActiveForm::begin([
        'id' => 'formAddAdminToUser',
        'action' => Url::to(['/client/users/add-admin', 'id' => $user->getId(), 'id_admin' => '']),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <?= $form->field($user, 'id_admin', [
                    'template' => '{input}',
                ])->widget(Select2::class, [
                    'data' => ArrayHelper::map($admins,'id','username'),
                    'options' => ['id' => 'selectAddAdminToUser'],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>
            </div>
            <div class="col-md-2"></div>
        </div>

        <div class="row" style="display:flex; justify-content: center;">
            <?= Html::submitButton('Сохранить', [
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#52BE7F',
                    'width' => '180px',
                    'height' => '40px',
                    'font-size' => '24px',
                    'border-radius' => '8px',
                ],
                'class' => 'btn btn-lg btn-success',
            ]) ?>
        </div>

    <?php ActiveForm::end(); ?>

<?php endif; ?>
