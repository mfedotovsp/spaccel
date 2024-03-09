<?php

use app\models\forms\SendEmailForm;
use app\models\forms\SingupForm;
use app\models\LoginForm;
use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Главная';

/**
 * @var User $user
 * @var LoginForm $model_login
 * @var SendEmailForm $model_send_email
 * @var SingupForm $model_signup
 */

?>
<div class="site-index">

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
                    <div class="mt-15 text-center">Для преакселерации и акселерации<br>стартап проектов</div>
                </div>

            </div>

            <?php if (Yii::$app->user->isGuest) : ?>


                <div class="row style_form_login">

                    <div class="col-md-12 text-center hello-text">Добро пожаловать!</div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login_user_form',
                        'action' => Url::to(['/site/login']),
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                    <div class="col-md-12">

                        <?= $form->field($model_login, 'identity', ['template' => '<div class="style-label">Логин</div><div>{input}</div>'])
                            ->label('Логин')
                            ->textInput([
                                'maxlength' => true,
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => '',
                                'autocomplete' => 'off'
                            ]) ?>

                    </div>

                    <div class="col-md-12" style="margin-top: 20px;">

                        <?= $form->field($model_login, 'password', ['template' => '<div class="style-label">Пароль</div><div>{input}</div>'])
                            ->passwordInput([
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => '',
                                'autocomplete' => 'off'
                            ]) ?>

                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 30px;">
                        <?= Html::submitButton('Войти', [
                            'class' => 'btn btn-default',
                            'name' => 'login-button',
                            'style' => [
                                'background' => '#E0E0E0',
                                'color' => '4F4F4F',
                                'border-radius' => '8px',
                                'width' => '140px',
                                'height' => '40px',
                                'font-size' => '18px',
                                'font-weight' => '700',
                                'text-transform' => 'uppercase',
                                'padding-top' => '9px'
                            ]
                        ]) ?>
                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 17px; margin-bottom: 10px; font-size: 14px;">или</div>

                    <div class="col-md-12 text-center">
                        <?= Html::a('Зарегистрироваться',['/site/registration'], [
                            'class' => 'link_singup',
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>


                <div class="row style_error_not_user">

                    <div class="col-md-12 text-center top-text">Не верный ввод!</div>

                    <div class="col-md-12 text-center text-content">Поля логин и пароль введены не верно или несоответствуют друг другу.</div>

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
                                'font-size' => '18px',
                                'font-weight' => '700',
                                'text-transform' => 'uppercase',
                                'padding-top' => '9px'
                            ]
                        ]) ?>

                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 17px; margin-bottom: 10px; font-size: 14px;">или</div>

                    <div class="col-md-12 text-center">
                        <?= Html::a('Вернуться назад',['#'], [
                            'onclick' => 'return false',
                            'class' => 'link_singup',
                            'id' => 'go_back_login_form',
                        ]) ?>
                    </div>

                </div>


                <div class="row style_go_password_recovery_for_email">

                    <div class="col-md-12 text-center top-text">Восстановление пароля</div>

                    <div class="col-md-12 text-center text-content">Введите адрес электронной почты (указанный при регистрации)</div>

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
                            'autocomplete' => 'off'
                        ]) ?>

                    </div>

                    <div class="col-md-12 text-center" style="margin-top: 58px;">

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

                    <div class="col-md-12 text-center" style="margin-top: 17px; margin-bottom: 10px; font-size: 14px;"">или</div>

                    <div class="col-md-12 text-center">
                        <?= Html::a('Вернуться назад',['#'], ['onclick' => 'return false', 'class' => 'link_singup', 'id' => 'go_to_back_login_form']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>


                <div class="row style_answer_for_password_recovery">

                    <div class="col-md-12 text-center title top-text"></div>

                    <div class="col-md-12 text-center text text-content"></div>

                    <div class="col-md-12 text-center link_back">
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


            <div class="content_main_page_block_text_desktop">

                <div>
                    <h1 class="top_title_main_page">Акселератор стартап-проектов</h1>
                </div>

                <div>
                    <div class="bottom_title_main_page">Customer Development <span>ШАГ</span> ЗА <span>ШАГОМ</span></div>
                </div>

                <div class="bottom_text_main_page">Для преакселерации и акселерации стартап проектов</div>

            </div>

        </div>

    </div>

</div>

<!--Модальные окна-->
<?php if (!Yii::$app->user->isGuest) {
    echo $this->render('_index_modal', ['user' => $user]);
} ?>
<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/site_index.js'); ?>