<?php


namespace app\modules\admin\controllers;

use app\controllers\AppController;
use app\models\ClientSettings;
use app\models\User;
use Yii;
use yii\filters\AccessControl;

class BehaviorsAdminPartController extends AppController
{
    /**
     * @return array[]
     */
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
                            $user = User::findOne(Yii::$app->user->getId());
                            $clientUser = $user->clientUser;
                            $clientSettings = ClientSettings::findOne(['client_id' => $clientUser->getClientId()]);
                            $admin = User::findOne($clientSettings->getAdminId());
                            return User::isUserAdmin($user->getUsername()) && User::isUserMainAdmin($admin->getUsername());
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserMainAdmin(Yii::$app->user->identity['username']);
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserDev(Yii::$app->user->identity['username']);
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserManager(Yii::$app->user->identity['username']);
                        }
                    ],
                ]

            ]

        ];
    }
}