<?php


namespace app\modules\expert\controllers;

use app\controllers\AppController;
use app\models\ClientSettings;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;

class BehaviorsExpertPartController extends AppController
{
    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $user = User::findOne(Yii::$app->user->getId());
        // Подключение шаблона администратора в экспертной части
        if (User::isUserDev($user->getUsername()) || User::isUserMainAdmin($user->getUsername())
            || User::isUserAdmin($user->getUsername())){
            $this->layout = '@app/modules/admin/views/layouts/main';
        }

        // Подключение шаблона трекера организации в экспертной части
        if (User::isUserAdmin($user->getUsername())) {
            $clientUser = $user->clientUser;
            $clientSettings = ClientSettings::findOne(['client_id' => $clientUser->getClientId()]);
            $admin = User::findOne($clientSettings->getAdminId());

            if (User::isUserMainAdmin($admin->getUsername())) {
                $this->layout = '@app/modules/admin/views/layouts/main-user';
            } else {
                $this->layout = '@app/modules/client/views/layouts/main-user';
            }
        }

        // Подключение шаблона админа организации в экспертной части
        if (User::isUserAdminCompany($user->getUsername())) {
            $this->layout = '@app/modules/client/views/layouts/main-user';
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
                            $user = User::findOne(Yii::$app->user->getId());
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserAdmin($user->getUsername()) && $isActiveClient;
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
                            $user = User::findOne(Yii::$app->user->id);
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserAdminCompany($user->getUsername()) && $isActiveClient;
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
                            $user = User::findOne(Yii::$app->user->id);
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserExpert($user->getUsername()) && $isActiveClient;
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['download'],
                        'verbs' => ['GET', 'POST'],
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