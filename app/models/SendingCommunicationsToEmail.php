<?php

namespace app\models;

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
            Yii::$app->mailer->compose('communications__UserAllowExpertiseToProject', ['communication' => $communication, 'role' => $mainAdmin->getRole()])
                ->setFrom([Yii::$app->params['supportEmail'] => 'Spaccel.ru - Акселератор стартап-проектов'])
                ->setTo($mainAdmin->getEmail())
                ->setSubject('Вам пришло новое уведомление на сайте Spaccel.ru')
                ->send();
        }

        if ($admin) {
            Yii::$app->mailer->compose('communications__UserAllowExpertiseToProject', ['communication' => $communication, 'role' => $admin->getRole()])
                ->setFrom([Yii::$app->params['supportEmail'] => 'Spaccel.ru - Акселератор стартап-проектов'])
                ->setTo($admin->getEmail())
                ->setSubject('Вам пришло новое уведомление на сайте Spaccel.ru')
                ->send();
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
            Yii::$app->mailer->compose('communications__UserAllowExpertiseToStageProject', ['communication' => $communication, 'role' => $expert->getRole()])
                ->setFrom([Yii::$app->params['supportEmail'] => 'Spaccel.ru - Акселератор стартап-проектов'])
                ->setTo($expert->getEmail())
                ->setSubject('Вам пришло новое уведомление на сайте Spaccel.ru')
                ->send();
        }

        if ($admin && $isSendTracker) {
            Yii::$app->mailer->compose('communications__UserAllowExpertiseToStageProject', ['communication' => $communication, 'role' => $admin->getRole()])
                ->setFrom([Yii::$app->params['supportEmail'] => 'Spaccel.ru - Акселератор стартап-проектов'])
                ->setTo($admin->getEmail())
                ->setSubject('Вам пришло новое уведомление на сайте Spaccel.ru')
                ->send();
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
            Yii::$app->mailer->compose('communications__UserSoftDeleteStageProject', ['communication' => $communication, 'role' => $expert->getRole()])
                ->setFrom([Yii::$app->params['supportEmail'] => 'Spaccel.ru - Акселератор стартап-проектов'])
                ->setTo($expert->getEmail())
                ->setSubject('Вам пришло новое уведомление на сайте Spaccel.ru')
                ->send();
        }

        if ($admin && $isSendTracker) {
            Yii::$app->mailer->compose('communications__UserSoftDeleteStageProject', ['communication' => $communication, 'role' => $admin->getRole()])
                ->setFrom([Yii::$app->params['supportEmail'] => 'Spaccel.ru - Акселератор стартап-проектов'])
                ->setTo($admin->getEmail())
                ->setSubject('Вам пришло новое уведомление на сайте Spaccel.ru')
                ->send();
        }
    }
}