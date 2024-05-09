<?php

namespace app\modules\contractor\controllers;

use app\controllers\AppController;
use app\models\User;
use scotthuangzl\export2excel\Export2ExcelBehavior;
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

        // Подключение шаблона для техподдержки, админа Spaccel в пользовательской части
        if (User::isUserDev($user->getUsername())
            || User::isUserMainAdmin($user->getUsername())) {
            $this->layout = '@app/modules/admin/views/layouts/main-user';
        }

        // Подключение шаблона админа организации в пользовательской части
        if (User::isUserAdminCompany($user->getUsername())) {
            $this->layout = '@app/modules/client/views/layouts/main-user';
        }

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        return [
            'export2excel' => [
                'class' => Export2ExcelBehavior::class,
            ],
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

                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = User::findOne(Yii::$app->user->id);
                            $isActiveClient = $user->clientUser->client->isActive();
                            return User::isUserMainAdmin($user->getUsername()) && $isActiveClient;
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
                ]
            ]
        ];
    }
}
