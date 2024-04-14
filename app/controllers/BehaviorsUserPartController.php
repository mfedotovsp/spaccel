<?php


namespace app\controllers;

use app\models\ClientSettings;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;

class BehaviorsUserPartController extends AppController
{

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!Yii::$app->user->isGuest) {

            $user = User::findOne(Yii::$app->user->getId());

            // Подключение шаблона для техподдержки, админа Spaccel и менеджера в пользовательской части
            if (User::isUserDev($user->getUsername())
                || User::isUserMainAdmin($user->getUsername())
                || User::isUserManager($user->getUsername())) {
                $this->layout = '@app/modules/admin/views/layouts/main-user';
            }

            // Подключение шаблона трекера организации в пользовательской части
            if (User::isUserAdmin($user->getUsername())) {
                $clientSettings = ClientSettings::findOne(['client_id' => $user->clientUser->getClientId()]);
                $admin = User::findOne($clientSettings->getAdminId());

                if (User::isUserMainAdmin($admin->getUsername())) {
                    $this->layout = '@app/modules/admin/views/layouts/main-user';
                } else {
                    $this->layout = '@app/modules/client/views/layouts/main-user';
                }
            }

            // Подключение шаблона админа организации в пользовательской части
            if (User::isUserAdminCompany($user->getUsername())) {
                $this->layout = '@app/modules/client/views/layouts/main-user';
            }

            // Подключение шаблона эксперта в пользовательской части
            if (User::isUserExpert($user->getUsername())) {
                $this->layout = '@app/modules/expert/views/layouts/main-user';
            }

            // Подключение шаблона исполнителя в пользовательской части
            if (User::isUserContractor($user->getUsername())) {
                $this->layout = '@app/modules/contractor/views/layouts/main-user';
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [

                'class' => AccessControl::class,

                /*Вызов исключения в случае отсутствия доступа*/
                /*'denyCallback' => function ($rule, $action) {
                    throw new \Exception('Нет доступа.');
                },*/

                'denyCallback' => function ($rule, $action) {
                    return $this->goHome();
                },

                'rules' => [

                    [
                        'allow' => true,
                        'controllers' => ['site', 'mailing'],
                        'actions' => ['get-form-registration', 'registration', 'singup', 'error', 'login', 'index', 'about', 'send-email', 'reset-password', 'activate-account', 'confidentiality-policy', 'download-presentation', 'unsubscribe'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?']
                    ],

                    [
                        'allow' => true,
                        'controllers' => ['site', 'mailing'],
                        'actions' => ['get-form-registration', 'registration', 'singup', 'error', 'login', 'index', 'about', 'send-email', 'reset-password', 'activate-account', 'confidentiality-policy', 'download-presentation', 'logout', 'unsubscribe'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->getId());
                            $isActiveClient = $user->clientUser->client->isActive();
                            return !$isActiveClient;
                        }
                    ],

                    [
                        'allow' => true,
                        'controllers' => ['site', 'profile', 'mailing'],
                        'actions' => ['singup', 'login', 'index', 'about', 'send-email', 'reset-password', 'update-profile', 'change-password',
                            'logout', 'project', 'roadmap', 'prefiles', 'not-found', 'unsubscribe'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return !User::isActiveStatus(Yii::$app->user->identity['username']);
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->getId());
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserSimple($user->getUsername()) && $isActiveClient;
                        }
                    ],

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
                            return User::isUserDev(Yii::$app->user->identity['username']);
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->getId());
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserExpert($user->getUsername()) && $isActiveClient;
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserManager(Yii::$app->user->identity['username']);
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->getId());
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserAdminCompany($user->getUsername()) && $isActiveClient;
                        }
                    ],

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->getId());
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserContractor($user->getUsername()) && $isActiveClient;
                        }
                    ],
                ]

            ]

        ];
    }
}
