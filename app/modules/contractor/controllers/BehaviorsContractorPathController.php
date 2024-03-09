<?php

namespace app\modules\contractor\controllers;

use app\controllers\AppController;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;

class BehaviorsContractorPathController extends AppController
{
    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $user = User::findOne(Yii::$app->user->getId());
        // Подключение шаблона проектанта в модуле исполнителя
        if (User::isUserSimple($user->getUsername())){
            $this->layout = '@app/views/layouts/main';
        }

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        return [
            'access' => [

                'class' => AccessControl::class,

                /*Вызов исключения в случае отсутствия доступа*/
                'denyCallback' => function ($rule, $action) {
                    return $this->goHome();
                },

                'rules' => [

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->id);
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserContractor($user->getUsername()) && $isActiveClient;
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->id);
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserSimple($user->getUsername()) && $isActiveClient;
                        }
                    ],
                ]
            ]
        ];
    }
}