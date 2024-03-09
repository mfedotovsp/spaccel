<?php

use app\models\User;
use yii\data\Pagination;
use yii\helpers\Html;
use app\models\MessageDevelopment;
use yii\widgets\LinkPager;

/** 
 * @var MessageDevelopment[] $messages 
 * @var Pagination $pagesMessages 
 * @var User $development 
 * @var User $user 
 */

?>

<div class="pagination-messages">
    <?= LinkPager::widget([
        'pagination' => $pagesMessages,
        'activePageCssClass' => 'pagination_active_page',
        'options' => ['class' => 'messages-pagination-list pagination'],
        'maxButtonCount' => 1,
    ]) ?>
</div>

<div class="text-center block_for_link_next_page_masseges">
    <?= Html::a('Посмотреть предыдущие сообщения', ['#'], ['class' => 'button_next_page_masseges'])?>
</div>

<?php $totalDateMessages = array(); // Массив общих дат сообщений ?>

<?php foreach ($messages as $i => $message) : ?>

    <?php
    // Вывод общих дат для сообщений
    if (!in_array($message->dayAndDateRus, $totalDateMessages, false)) {
        $totalDateMessages[] = $message->dayAndDateRus;
        echo '<div class="dayAndDayMessage">'.$message->dayAndDateRus.'</div>';
    }
    ?>

    <?php if ($message->getSenderId() !== $user->getId()) : ?>

        <?php if ($message->getStatus() === MessageDevelopment::NO_READ_MESSAGE) : ?>

            <div class="message addressee-expert unreadmessage" id="message_id-<?= $message->getId() ?>">

                <?php if ($development->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$development->getId().'/avatar/'.$development->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div>Техническая поддержка</div>
                        <div>
                            <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                            <?= date('H:i', $message->getCreatedAt()) ?>
                        </div>
                    </div>

                    <div class="message-description">

                        <?php if ($message->getDescription()) : ?>
                            <?= $message->getDescription() ?>
                        <?php endif; ?>

                        <?php if ($message->files) : ?>
                            <div class="message-description-files">
                                <?php foreach ($message->files as $file) : ?>
                                    <div>
                                        <?= Html::a($file->getFileName(), ['/expert/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        <?php else : ?>

            <div class="message addressee-expert" id="message_id-<?= $message->getId() ?>">

                <?php if ($development->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$development->getId().'/avatar/'.$development->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div>Техническая поддержка</div>
                        <div>
                            <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                            <?= date('H:i', $message->getCreatedAt()) ?>
                        </div>
                    </div>

                    <div class="message-description">

                        <?php if ($message->getDescription()) : ?>
                            <?= $message->getDescription() ?>
                        <?php endif; ?>

                        <?php if ($message->files) : ?>
                            <div class="message-description-files">
                                <?php foreach ($message->files as $file) : ?>
                                    <div>
                                        <?= Html::a($file->getFileName(), ['/expert/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()])?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        <?php endif; ?>

    <?php else : ?>

        <?php if ($message->getStatus() === MessageDevelopment::NO_READ_MESSAGE) : ?>

            <div class="message addressee-development unreadmessage" id="message_id-<?= $message->getId() ?>">

                <?php if ($user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div class="interlocutor"><?= $user->getUsername() ?></div>
                        <div>
                            <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                            <?= date('H:i', $message->getCreatedAt()) ?>
                        </div>
                    </div>

                    <div class="message-description">

                        <?php if ($message->getDescription()) : ?>
                            <?= $message->getDescription() ?>
                        <?php endif; ?>

                        <?php if ($message->files) : ?>
                            <div class="message-description-files">
                                <?php foreach ($message->files as $file) : ?>
                                    <div>
                                        <?= Html::a($file->getFileName(), ['/expert/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        <?php else : ?>

            <div class="message addressee-development" id="message_id-<?= $message->getId() ?>">

                <?php if ($user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div class="interlocutor"><?= $user->getUsername() ?></div>
                        <div>
                            <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                            <?= date('H:i', $message->getCreatedAt()) ?>
                        </div>
                    </div>

                    <div class="message-description">

                        <?php if ($message->getDescription()) : ?>
                            <?= $message->getDescription() ?>
                        <?php endif; ?>

                        <?php if ($message->files) : ?>
                            <div class="message-description-files">
                                <?php foreach ($message->files as $file) : ?>
                                    <div>
                                        <?= Html::a($file->getFileName(), ['/expert/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        <?php endif; ?>

    <?php endif; ?>

<?php endforeach; ?>
