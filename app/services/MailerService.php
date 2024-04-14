<?php

namespace app\services;

use app\models\EmailLogs;
use app\models\EmailUnsubscribers;
use Yii;

class MailerService
{
    /**
     * @param string $email
     * @param string $subject
     * @param string $view
     * @param array $params
     * @return bool
     */
    public static function send(string $email, string $subject, string $view, array $params = []): bool
    {
        $unsubscribeLink = Yii::$app->params['siteUrl'] . "/mailing/unsubscribe/?email={$email}&hash=" . md5('ms-' . $email);
        $params['unsubscribeLink'] = $unsubscribeLink;

        $mailerLog = new EmailLogs();
        $mailerLog->setEmail($email);
        $mailerLog->setSubject($subject);
        $mailerLog->setBodyHtml(Yii::$app->mailer->render($view, $params));

        if (EmailUnsubscribers::find()->andWhere(['email' => $email])->exists()) {
            $mailerLog->setIsFailed(true);
            $mailerLog->setError('Пользователь отписался от email-рассылки');
            $mailerLog->save(false);

            return true;
        }

        try {
            Yii::$app->mailer->compose($view, $params)
                ->addHeader('List-Unsubscribe', $unsubscribeLink)
                ->setSubject($subject)
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['siteName'] . ' - Акселератор стартап-проектов'])
                ->setTo($email)
                ->send();

            $mailerLog->save(false);

            return true;
        }
        catch (\Throwable $e) {
            $error = iconv(mb_detect_encoding($e->getMessage(), mb_detect_order(), true), "UTF-8", $e->getMessage());
            $mailerLog->setIsFailed(true);
            $mailerLog->setError("[error] {$error} [code] {$e->getCode()}");
            $mailerLog->save(false);

            return false;
        }
    }
}
