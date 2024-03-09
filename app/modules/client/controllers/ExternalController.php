<?php

namespace app\modules\client\controllers;

use app\models\PatternHttpException;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class ExternalController extends AppClientController
{
    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        if (User::isUserAdminCompany(Yii::$app->user->identity['username'])) {
            return parent::beforeAction($action);
        }

        PatternHttpException::noAccess();
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}