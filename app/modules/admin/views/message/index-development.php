<?php

use app\models\ConversationDevelopment;
use app\modules\admin\models\form\SearchForm;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\User;

$this->title = 'Сообщения';
$this->registerCssFile('@web/css/admin-message-index.css');

/**
 * @var User $development
 * @var SearchForm $searchForm
 * @var ConversationDevelopment[] $allConversations
 */

?>

<div class="admin-message-index">

    <!--Preloader begin-->
    <div id="preloader">
        <div id="cont">
            <div class="round"></div>
            <div class="round"></div>
            <div class="round"></div>
            <div class="round"></div>
        </div>
        <div id="loading">Loading</div>
    </div>
    <!--Preloader end-->

    <div class="row message_menu">

        <div class="col-sm-6 col-lg-4 search-block">

            <?php $form = ActiveForm::begin([
                'id' => 'search_user_conversation',
                'action' => Url::to(['/admin/message/get-development-conversation-query', 'id' => $development->getId()]),
                'options' => ['class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

                <?= $form->field($searchForm, 'search', ['template' => '{input}'])
                    ->textInput([
                        'id' => 'search_conversation',
                        'placeholder' => 'Поиск',
                        'class' => 'style_form_field_respond',
                        'autocomplete' => 'off'])
                    ->label(false) ?>

            <?php ActiveForm::end(); ?>

            <!--Беседы полученные в запросе поиска (по умолчанию это все доступные пользователи)-->
            <div class="conversations_query" id="conversations_query">
                <!--Сюда добавляем результат поиска-->
            </div>

        </div>

        <div class="col-sm-6 col-lg-8">

        </div>

    </div>

    <div class="row all_content_messages">

        <div class="col-sm-6 col-lg-4 conversation-list-menu">

            <div id="conversation-list-menu">

                <!--Блок для бесед со всеми пользователями-->
                <div class="containerForAllConversations">

                    <?= $this->render('update_conversations_for_development', [
                            'allConversations' => $allConversations
                    ]) ?>

                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-8">
            <div class="message_index_block_right_info">
                Выберите пользователя (перейдите к беседе с пользователем)
            </div>
        </div>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/development_message_index.js'); ?>
