<?php

use app\models\forms\SendEmailForm;
use app\models\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Подтверждение регистрации';

/**
 * @var LoginForm $model_login
 * @var SendEmailForm $model_send_email
 */

?>
<div class="site-activate-account">

    <div class="background_for_main_page">

        <div class="row top_line_text_main_page">
            <div class="col-md-12">Инструмент для Прокачки бизнес-идеи, поиска потребительского сегмента, формирования продукта</div>
        </div>

        <div class="content_main_page">

            <?php if (Yii::$app->user->isGuest) : ?>

                <div class="row style_form_login">

                    <div class="col-md-12 text-center" style="font-size: 20px; margin: 25px 0 20px 0;">Произошла ошибка</div>

                    <div class="col-md-12 text-center" style=" margin: 0 0 15px 0;">Ссылка подтверждения регистрации была просрочена или изменена.</div>

                    <div class="col-md-12 text-center" style=" margin: 0 0 15px 0;">Заполните форму и получите новое письмо для подтверждения.</div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login_user_form',
                        'action' => Url::to(['/site/login']),
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                    <div class="col-md-12">

                        <?= $form->field($model_login, 'identity', ['template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Логин или email</div><div>{input}</div>'])
                            ->label('Логин')
                            ->textInput([
                                'maxlength' => true,
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Введите логин или email',
                                'autocomplete' => 'off'
                            ]) ?>

                    </div>

                    <div class="col-md-12">

                        <?= $form->field($model_login, 'password', ['template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Пароль</div><div>{input}</div>'])
                            ->passwordInput([
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Введите пароль',
                                'autocomplete' => 'off'
                            ]) ?>

                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 15px;">
                        <?= Html::submitButton('Отправить', [
                            'class' => 'btn btn-default',
                            'name' => 'login-button',
                            'style' => [
                                'background' => '#E0E0E0',
                                'color' => '4F4F4F',
                                'border-radius' => '8px',
                                'width' => '140px',
                                'height' => '40px',
                                'font-size' => '16px',
                                'font-weight' => '700'
                            ]
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>


                <div class="row style_error_not_user">

                    <div class="col-md-12 text-center" style="font-size: 20px; margin: 25px 0 45px 0;">Не верный ввод!</div>

                    <div class="col-md-12 text-center" style=" margin: 45px 0 70px 0;">Поля логин и пароль введены не верно или несоответствуют друг другу.</div>

                    <div class="col-md-12 text-center" style="margin-top: 30px;">

                        <?= Html::button('Забыли пароль?', [
                            'id' => 'go_password_recovery_for_email',
                            'class' => 'btn btn-default',
                            'style' => [
                                'background' => '#E0E0E0',
                                'color' => '4F4F4F',
                                'border-radius' => '8px',
                                'width' => '170px',
                                'height' => '40px',
                                'font-size' => '16px',
                                'font-weight' => '700'
                            ]
                        ]) ?>

                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 15px; margin-bottom: 5px;">или</div>

                    <div class="col-md-12 text-center">
                        <?= Html::a('Вернуться назад',['#'], [
                            'onclick' => 'return false',
                            'class' => 'link_singup',
                            'id' => 'go_back_login_form',
                        ]) ?>
                    </div>

                </div>


                <div class="row style_go_password_recovery_for_email">

                    <div class="col-md-12 text-center" style="font-size: 20px; margin: 25px 0 45px 0;">Восстановление пароля</div>

                    <div class="col-md-12 text-center" style="margin: 45px 0 15px 0;">Введите адрес электронной почты (указанный при регистрации)</div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'form_send_email',
                        'action' => Url::to(['/site/send-email']),
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                    <div class="col-md-12" style="margin-top: 5px;">

                        <?= $form->field($model_send_email, 'email')->label(false)->input('email',[
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Введите email',
                        ]) ?>

                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 45px;">

                        <?= Html::submitButton('Отправить', [
                            'class' => 'btn btn-default',
                            'name' => 'send-email-button',
                            'style' => [
                                'background' => '#E0E0E0',
                                'color' => '4F4F4F',
                                'border-radius' => '8px',
                                'width' => '170px',
                                'height' => '40px',
                                'font-size' => '16px',
                                'font-weight' => '700'
                            ]
                        ]) ?>

                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 15px; margin-bottom: 5px;">или</div>

                    <div class="col-md-12 text-center">
                        <?= Html::a('Вернуться назад',['#'], ['onclick' => 'return false', 'class' => 'link_singup', 'id' => 'go_to_back_login_form']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>


                <div class="row style_answer_for_password_recovery">

                    <div class="col-md-12 text-center title" style="font-size: 20px; margin: 25px 0 45px 0;"></div>

                    <div class="col-md-12 text-center text" style="margin: 45px 0 0 0;"></div>

                    <div class="col-md-12 text-center link_back" style="position: absolute; bottom: 0; height: 45px;">
                        <?= Html::a('Вернуться назад',['#'], ['onclick' => 'return false', 'class' => 'link_singup', 'id' => 'go2_to_back_login_form']) ?>
                    </div>

                </div>


                <div class="row style_error_not_confirm_singup">

                    <div class="col-md-12 text-center" style="font-size: 20px; margin: 25px 0 45px 0;">Подтвердите регистрацию</div>

                    <div class="col-md-12 text-center ajax-message" style="margin: 45px 0 70px 0;"></div>

                    <div class="col-md-12 text-center" style="position: absolute; bottom: 0; height: 45px;">
                        <?= Html::a('Вернуться назад',['#'], [
                            'onclick' => 'return false',
                            'class' => 'link_singup',
                            'id' => 'go4_to_back_login_form',
                        ]) ?>
                    </div>

                </div>


            <?php endif;?>


            <div class="content_main_page_block_text">

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
<?php $this->registerJsFile('@web/js/site_activate_account.js'); ?>