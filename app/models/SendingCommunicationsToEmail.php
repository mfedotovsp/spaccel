<?php

namespace app\models;

use app\services\MailerService;
use Yii;

class SendingCommunicationsToEmail
{

    /**
     * Отправка письма с уведомлением о разрешении экпертизы
     * по проекту на почту админу организации и трекеру
     *
     * @param ProjectCommunications $communication
     * @return void
     */
    public static function allowExpertiseToProject(ProjectCommunications $communication): void
    {
        $user = User::findOne($communication->getSenderId());
        $mainAdmin = $user->mainAdmin;
        $admin = $user->admin;

        if ($mainAdmin) {
            MailerService::send(
                $mainAdmin->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__UserAllowExpertiseToProject',
                ['communication' => $communication, 'role' => $mainAdmin->getRole()]
            );
        }

        if ($admin) {
            MailerService::send(
                $admin->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__UserAllowExpertiseToProject',
                ['communication' => $communication, 'role' => $admin->getRole()]
            );
        }
    }


    /**
     * Отправка письма с уведомлением о разрешении экпертизы
     * по этапу проекта на почту эксперту и трекеру
     *
     * @param ProjectCommunications $communication
     * @param bool $isSendTracker
     * @return void
     */
    public static function allowExpertiseToStageProject(ProjectCommunications $communication, bool $isSendTracker = false): void
    {
        $user = $communication->user;
        $expert = $communication->expert;
        $admin = $user->admin;

        if ($expert) {
            MailerService::send(
                $expert->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__UserAllowExpertiseToStageProject',
                ['communication' => $communication, 'role' => $expert->getRole()]
            );
        }

        if ($admin && $isSendTracker) {
            MailerService::send(
                $admin->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__UserAllowExpertiseToStageProject',
                ['communication' => $communication, 'role' => $admin->getRole()]
            );
        }
    }


    /**
     * Отправка письма с уведомлением об удалении
     * этапа проекта на почту эксперту и трекеру
     *
     * @param ProjectCommunications $communication
     * @param bool $isSendTracker
     * @return void
     */
    public static function softDeleteStageProject(ProjectCommunications $communication, bool $isSendTracker = false): void
    {
        $user = $communication->user;
        $expert = $communication->expert;
        $admin = $user->admin;

        if ($expert) {
            MailerService::send(
                $expert->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__UserSoftDeleteStageProject',
                ['communication' => $communication, 'role' => $expert->getRole()]
            );
        }

        if ($admin && $isSendTracker) {
            MailerService::send(
                $admin->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__UserSoftDeleteStageProject',
                ['communication' => $communication, 'role' => $admin->getRole()]
            );
        }
    }
}
