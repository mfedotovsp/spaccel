<?php

use app\models\ClientSettings;
use app\models\ClientUser;
use app\models\ConversationDevelopment;
use yii\helpers\Html;
use app\models\User;

/**
 * @var ConversationDevelopment[] $allConversations
 */

?>

<?php if ($allConversations) : ?>

    <?php foreach ($allConversations as $conversation) : ?>

        <?php if (User::isUserSimple($conversation->user->getUsername())) : ?>

            <div class="container-user_messages" id="conversation-<?= $conversation->getId() ?>">

                <!--Проверка существования аватарки-->
                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Кол-во непрочитанных сообщений от пользователя-->
                <?php if ($conversation->user->countUnreadMessagesDevelopmentFromUser) : ?>
                    <div class="countUnreadMessagesSender active"><?= $conversation->user->countUnreadMessagesDevelopmentFromUser ?></div>
                <?php else : ?>
                    <div class="countUnreadMessagesSender"></div>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($conversation->user->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="container_user_messages_text_content">

                    <div class="row block_top">

                        <div class="col-xs-8"><?= $conversation->user->getUsername() ?></div>

                        <div class="col-xs-4 text-right">
                            <?php if ($conversation->lastMessage) : ?>
                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($conversation->lastMessage) : ?>
                        <div class="block_bottom_exist_message">

                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                            <?php else : ?>
                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                            <?php endif; ?>

                            <div>
                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                    <?= $conversation->lastMessage->getDescription() ?>
                                <?php else : ?>
                                    ...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                    <?php endif; ?>

                </div>
            </div>

        <?php elseif (User::isUserManager($conversation->user->getUsername())) : ?>

            <div class="container-user_messages" id="adminConversation-<?= $conversation->getId() ?>">

                <!--Проверка существования аватарки-->
                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Кол-во непрочитанных сообщений от пользователя-->
                <?php if ($conversation->user->countUnreadMessagesDevelopmentFromUser) : ?>
                    <div class="countUnreadMessagesSender active"><?= $conversation->user->countUnreadMessagesDevelopmentFromUser ?></div>
                <?php else : ?>
                    <div class="countUnreadMessagesSender"></div>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($conversation->user->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="container_user_messages_text_content">

                    <div class="row block_top">

                        <div class="col-xs-8"><?= $conversation->user->getUsername() ?></div>

                        <div class="col-xs-4 text-right">
                            <?php if ($conversation->lastMessage) : ?>
                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($conversation->lastMessage) : ?>
                        <div class="block_bottom_exist_message">

                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                            <?php else : ?>
                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                            <?php endif; ?>

                            <div>
                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                    <?= $conversation->lastMessage->getDescription() ?>
                                <?php else : ?>
                                    ...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                    <?php endif; ?>

                </div>
            </div>

        <?php elseif (User::isUserExpert($conversation->user->getUsername())) : ?>

            <div class="container-user_messages" id="expertConversation-<?= $conversation->getId() ?>">

                <!--Проверка существования аватарки-->
                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Кол-во непрочитанных сообщений от пользователя-->
                <?php if ($conversation->user->countUnreadMessagesDevelopmentFromUser) : ?>
                    <div class="countUnreadMessagesSender active"><?= $conversation->user->countUnreadMessagesDevelopmentFromUser ?></div>
                <?php else : ?>
                    <div class="countUnreadMessagesSender"></div>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($conversation->user->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="container_user_messages_text_content">

                    <div class="row block_top">

                        <div class="col-xs-8"><?= $conversation->user->getUsername() ?></div>

                        <div class="col-xs-4 text-right">
                            <?php if ($conversation->lastMessage) : ?>
                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($conversation->lastMessage) : ?>
                        <div class="block_bottom_exist_message">

                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                            <?php else : ?>
                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                            <?php endif; ?>

                            <div>
                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                    <?= $conversation->lastMessage->getDescription() ?>
                                <?php else : ?>
                                    ...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                    <?php endif; ?>

                </div>
            </div>

        <?php elseif (User::isUserAdmin($conversation->user->getUsername())) : ?>

            <?php
            /** @var ClientUser $clientUser */
            $clientUser = $conversation->user->clientUser;
            $clientSettings = ClientSettings::findOne(['client_id' => $clientUser->getClientId()]);
            $adminCompany = User::findOne(['id' => $clientSettings->getAdminId()]);
            ?>

            <?php if (User::isUserMainAdmin($adminCompany->getUsername())) : ?>

                <div class="container-user_messages" id="adminConversation-<?= $conversation->getId() ?>">

            <?php else : ?>

                <div class="container-user_messages" id="clientAdminConversation-<?= $conversation->getId() ?>">

            <?php endif; ?>

                <!--Проверка существования аватарки-->
                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Кол-во непрочитанных сообщений от пользователя-->
                <?php if ($conversation->user->countUnreadMessagesDevelopmentFromUser) : ?>
                    <div class="countUnreadMessagesSender active"><?= $conversation->user->countUnreadMessagesDevelopmentFromUser ?></div>
                <?php else : ?>
                    <div class="countUnreadMessagesSender"></div>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($conversation->user->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="container_user_messages_text_content">

                    <div class="row block_top">

                        <div class="col-xs-8"><?= $conversation->user->getUsername() ?></div>

                        <div class="col-xs-4 text-right">
                            <?php if ($conversation->lastMessage) : ?>
                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($conversation->lastMessage) : ?>
                        <div class="block_bottom_exist_message">

                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                            <?php else : ?>
                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                            <?php endif; ?>

                            <div>
                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                    <?= $conversation->lastMessage->getDescription() ?>
                                <?php else : ?>
                                    ...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                    <?php endif; ?>

                </div>
            </div>

        <?php elseif (User::isUserMainAdmin($conversation->user->getUsername())) : ?>

            <div class="container-user_messages" id="adminConversation-<?= $conversation->getId() ?>">

                <!--Проверка существования аватарки-->
                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Кол-во непрочитанных сообщений от пользователя-->
                <?php if ($conversation->user->countUnreadMessagesDevelopmentFromUser) : ?>
                    <div class="countUnreadMessagesSender active"><?= $conversation->user->countUnreadMessagesDevelopmentFromUser ?></div>
                <?php else : ?>
                    <div class="countUnreadMessagesSender"></div>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($conversation->user->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="container_user_messages_text_content">

                    <div class="row block_top">

                        <div class="col-xs-8"><?= $conversation->user->getUsername() ?></div>

                        <div class="col-xs-4 text-right">
                            <?php if ($conversation->lastMessage) : ?>
                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($conversation->lastMessage) : ?>
                        <div class="block_bottom_exist_message">

                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                            <?php else : ?>
                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                            <?php endif; ?>

                            <div>
                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                    <?= $conversation->lastMessage->getDescription() ?>
                                <?php else : ?>
                                    ...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                    <?php endif; ?>

                </div>
            </div>

        <?php elseif (User::isUserAdminCompany($conversation->user->getUsername())) : ?>

            <div class="container-user_messages" id="clientAdminConversation-<?= $conversation->getId() ?>">

                <!--Проверка существования аватарки-->
                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Кол-во непрочитанных сообщений от пользователя-->
                <?php if ($conversation->user->countUnreadMessagesDevelopmentFromUser) : ?>
                    <div class="countUnreadMessagesSender active"><?= $conversation->user->countUnreadMessagesDevelopmentFromUser ?></div>
                <?php else : ?>
                    <div class="countUnreadMessagesSender"></div>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($conversation->user->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="container_user_messages_text_content">

                    <div class="row block_top">

                        <div class="col-xs-8"><?= $conversation->user->getUsername() ?></div>

                        <div class="col-xs-4 text-right">
                            <?php if ($conversation->lastMessage) : ?>
                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($conversation->lastMessage) : ?>
                        <div class="block_bottom_exist_message">

                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                            <?php else : ?>
                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                            <?php endif; ?>

                            <div>
                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                    <?= $conversation->lastMessage->getDescription() ?>
                                <?php else : ?>
                                    ...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                    <?php endif; ?>

                </div>
            </div>

        <?php endif; ?>

    <?php endforeach; ?>

<?php else : ?>

    <div class="text-center block_not_conversations">Нет доступных пользователей</div>

<?php endif; ?>
