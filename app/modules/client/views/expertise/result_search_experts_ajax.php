<?php

use app\models\Projects;
use app\models\User;
use yii\helpers\Html;
use app\models\ExpertType;
use app\models\CommunicationTypes;
use app\models\ProjectCommunications;
use yii\helpers\Url;

/**
 * @var User[] $experts
 * @var int $project_id
 */

?>

<!--Поиск экспертов-->
<?php if ($experts) : ?>

    <!--Заголовки для списка экспертов-->
    <div class="row headers_data_experts">

        <div class="col-md-3">Логин эксперта</div>

        <div class="col-md-3">Сфера профессиональной компетенции</div>

        <div class="col-md-3">Тип экпертной деятельности</div>

        <div class="col-md-2">Ключевые слова</div>

        <div class="col-md-1"></div>

    </div>

    <?php foreach ($experts as $expert) : ?>

        <div class="row container-one_user user_container_number-<?=$expert->getId() ?>">

            <div class="col-md-3 column-user-fio" id="link_user_profile-<?= $expert->getId() ?>" title="Перейти в профиль эксперта">

                <!--Проверка существования аватарки-->
                <?php if ($expert->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$expert->getId().'/avatar/'.$expert->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($expert->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="block-fio-and-date-last-visit">
                    <div class="block-fio"><?= $expert->getUsername() ?></div>
                    <div class="block-date-last-visit">
                        <?php if(is_string($expert->checkOnline)) : ?>
                            Пользователь был в сети <?= $expert->checkOnline ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="col-md-3 text_description" title="<?= $expert->expertInfo->getScopeProfessionalCompetence() ?>">
                <?= $expert->expertInfo->getScopeProfessionalCompetence() ?>
            </div>

            <div class="col-md-3 text_description" title="<?= ExpertType::getContent($expert->expertInfo->getType()) ?>">
                <?= ExpertType::getContent($expert->expertInfo->getType()) ?>
            </div>

            <div class="col-md-2 text_description" title="<?= $expert->keywords->getDescription() ?>">
                <?= $expert->keywords->getDescription() ?>
            </div>

            <div class="col-md-1">

                <div class="row pull-right">

                    <?php if (ProjectCommunications::isNeedAskExpert($expert->getId(), $project_id)) : ?>

                        <?php /** @var $project Projects */
                        $project = Projects::find(false)
                            ->andWhere(['id' => $project_id])
                            ->one();

                        if (!$project->getDeletedAt()): ?>

                            <?= Html::a('Сделать запрос', Url::to([
                                '/client/communications/send',
                                'adressee_id' => $expert->getId(),
                                'project_id' => $project_id,
                                'type' => CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE
                            ]), [
                                'class' => 'btn btn-default send-communication',
                                'id' => 'send_communication-'.$expert->getId(),
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'color' => '#FFFFFF',
                                    'background' => '#52BE7F',
                                    'width' => '140px',
                                    'height' => '40px',
                                    'font-size' => '18px',
                                    'border-radius' => '8px',
                                ]
                            ]) ?>

                        <?php endif; ?>

                    <?php else : ?>

                        <div class="text-success">Запрос сделан</div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

<?php else : ?>

    <h4 class="text-center">По вашему запросу не найдены эксперты...</h4>

<?php endif; ?>
