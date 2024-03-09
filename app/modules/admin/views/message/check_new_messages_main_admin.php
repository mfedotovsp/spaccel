<?php

use app\models\User;
use yii\helpers\Html;
use app\modules\admin\models\MessageMainAdmin;

/**
 * @var MessageMainAdmin[] $messages
 * @var User $main_admin
 * @var User $admin
 * @var MessageMainAdmin $lastMessageOnPage
 */

?>

<?php $totalDateMessages = array(); // Массив общих дат сообщений ?>
<?php $totalDateMessages[] = $lastMessageOnPage->dayAndDateRus; ?>

<?php foreach ($messages as $i => $message) : ?>

    <?php
    // Вывод общих дат для сообщений
    if (!in_array($message->dayAndDateRus, $totalDateMessages, false)) {
        $totalDateMessages[] = $message->dayAndDateRus;
        echo '<div class="dayAndDayMessage">'.$message->dayAndDateRus.'</div>';
    }
    ?>

    <?php if ($message->getSenderId() !== $admin->getId()) : ?>

        <?php if ($message->getStatus() === MessageMainAdmin::NO_READ_MESSAGE) : ?>

            <div class="message addressee-admin unreadmessage" id="message_id-<?= $message->getId() ?>">

                <?php if ($main_admin->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$main_admin->getId().'/avatar/'.$main_admin->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div>Главный администратор</div>
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
                                        <?= Html::a($file->getFileName(), ['/admin/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        <?php else : ?>

            <div class="message addressee-admin" id="message_id-<?= $message->getId() ?>">

                <?php if ($main_admin->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$main_admin->getId().'/avatar/'.$main_admin->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div>Главный администратор</div>
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
                                        <?= Html::a($file->getFileName(), ['/admin/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        <?php endif; ?>

    <?php else : ?>

        <?php if ($message->getStatus() === MessageMainAdmin::NO_READ_MESSAGE) : ?>

            <div class="message addressee-main_admin unreadmessage" id="message_id-<?= $message->getId() ?>">

                <?php if ($admin->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$admin->getId().'/avatar/'.$admin->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div class="interlocutor"><?= $admin->getUsername() ?></div>
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
                                        <?= Html::a($file->getFileName(), ['/admin/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        <?php else : ?>

            <div class="message addressee-main_admin" id="message_id-<?= $message->getId() ?>">

                <?php if ($admin->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$admin->getId().'/avatar/'.$admin->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                <?php endif; ?>

                <div class="sender_data">
                    <div class="sender_info">
                        <div class="interlocutor"><?= $admin->getUsername() ?></div>
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
                                        <?= Html::a($file->getFileName(), ['/admin/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
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
