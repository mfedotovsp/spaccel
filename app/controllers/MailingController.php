<?php

namespace app\controllers;

use app\models\EmailUnsubscribers;
use app\models\PatternHttpException;
use app\models\User;
use yii\web\HttpException;

/**
 * Class MailingController
 * @package app\controllers
 */
class MailingController extends AppUserPartController
{
    /**
     * @param string $email
     * @param string $hash
     * @return string
     * @throws HttpException
     */
    public function actionUnsubscribe(string $email, string $hash): string
    {
        if (empty($email) || md5('ms-' . $email) !== $hash) {
            PatternHttpException::noData();
        }

        $existUnsubscribe = EmailUnsubscribers::find()
            ->andWhere(['email' => $email])
            ->exists();

        if (!$existUnsubscribe && User::findOne(['email' => $email])) {
            $emailUnsubscribe = new EmailUnsubscribers();
            $emailUnsubscribe->setEmail($email);
            $emailUnsubscribe->save();
        }

        $this->layout = false;

        return $this->render('unsubscribe', compact('email'));
    }
}
