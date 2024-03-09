<?php

use app\models\forms\SendEmailForm;
use app\models\ResetPasswordForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Востановление пароля';

/**
 * @var ResetPasswordForm $model
 * @var SendEmailForm $model_send_email
 */

?>

<div class="site-resetPassword">

    <div class="background_for_main_page">

        <div class="row top_line_text_main_page">
            <div class="col-md-12">Инструмент для Прокачки бизнес-идеи, поиска потребительского сегмента, формирования продукта</div>
        </div>

        <div class="content_main_page">

            <div class="content_main_page_block_text_mobile">

                <div>
                    <h1 class="top_title_main_page">Акселератор<br>стартап-проектов</h1>
                </div>

                <div>
                    <div class="bottom_title_main_page">Customer Development<br><span>ШАГ</span> ЗА <span>ШАГОМ</span></div>
                </div>

            </div>

            <?php if (Yii::$app->user->isGuest) : ?>

                <?php if ($model->exist === true) : ?>

                    <div class="row style_form_reset_password">

                        <div class="col-md-12 text-center top-text">Восстановление пароля</div>

                        <div class="col-md-12 text-center text-content">Введите в поле новый пароль.</div>

                        <?php $form = ActiveForm::begin([
                            'id' => 'reset_password_form',
                            'action' => Url::to(['/site/reset-password', 'key' => Yii::$app->request->get('key')]),
                            'options' => ['class' => 'g-py-15'],
                            'errorCssClass' => 'u-has-error-v1',
                            'successCssClass' => 'u-has-success-v1-1',
                        ]); ?>

                        <div class="col-md-12">

                            <?= $form->field($model, 'password')->label(false)
                                ->passwordInput([
                                    'minlength' => 6,
                                    'maxlength' => 32,
                                    'required' => true,
                                    'class' => 'style_form_field_respond form-control',
                                    'placeholder' => '',
                                    'autocomplete' => 'off'
                                ]) ?>

                        </div>

                        <div class="col-md-12 text-center" style="margin-top: 100px;">
                            <?= Html::submitButton('Сохранить', [
                                'class' => 'btn btn-default',
                                'style' => [
                                    'background' => '#E0E0E0',
                                    'color' => '4F4F4F',
                                    'border-radius' => '8px',
                                    'width' => '170px',
                                    'height' => '40px',
                                    'font-size' => '18px',
                                    'font-weight' => '700',
                                    'text-transform' => 'uppercase',
                                    'padding-top' => '9px'
                                ]
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>

                <?php else : ?>

                    <div class="row style_password_recovery_for_email">

                        <div class="col-md-12 text-center top-text">Восстановление пароля</div>

                        <div class="col-md-12 text-center text-content">Ссылка на восстановление пароля была просрочена или изменена.</div>

                        <div class="col-md-12 text-center text-bottom">Для повторной отправки письма введите адрес электронной почты (указанный при регистрации).</div>

                        <?php $form = ActiveForm::begin([
                            'id' => 'form_send_email',
                            'action' => Url::to(['/site/send-email']),
                            'options' => ['class' => 'g-py-15'],
                            'errorCssClass' => 'u-has-error-v1',
                            'successCssClass' => 'u-has-success-v1-1',
                        ]); ?>

                        <div class="col-md-12">

                            <?= $form->field($model_send_email, 'email')->label(false)->input('email',[
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Введите email',
                                'autocomplete' => 'off'
                            ]) ?>

                        </div>

                        <div class="col-md-12 text-center" style="margin-top: 60px;">

                            <?= Html::submitButton('Отправить', [
                                'class' => 'btn btn-default',
                                'name' => 'send-email-button',
                                'style' => [
                                    'background' => '#E0E0E0',
                                    'color' => '4F4F4F',
                                    'border-radius' => '8px',
                                    'width' => '170px',
                                    'height' => '40px',
                                    'font-size' => '18px',
                                    'font-weight' => '700',
                                    'text-transform' => 'uppercase',
                                    'padding-top' => '9px'
                                ]
                            ]) ?>

                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>

                    <div class="row style_answer_for_password_recovery">

                        <div class="col-md-12 text-center title top-text"></div>

                        <div class="col-md-12 text-center text text-content"></div>

                        <div class="col-md-12 text-center link_back">
                            <?= Html::a('Вернуться назад',['#'], ['onclick' => 'return false', 'class' => 'link_singup', 'id' => 'go_back_password_recovery_for_email']) ?>
                        </div>

                    </div>

                <?php endif; ?>

            <?php endif;?>

            <div class="content_main_page_block_text_desktop">

                <div>
                    <h1 class="top_title_main_page">Акселератор стартап-проектов</h1>
                </div>

                <div>
                    <div class="bottom_title_main_page">Customer Development <span>ШАГ</span> ЗА <span>ШАГОМ</span></div>
                </div>

            </div>
        </div>
    </div>
</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/site_reset_password.js'); ?>