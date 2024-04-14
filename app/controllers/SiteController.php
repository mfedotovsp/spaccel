<?php

namespace app\controllers;

use app\models\Client;
use app\models\ClientCodes;
use app\models\ClientCodeTypes;
use app\models\ClientRatesPlan;
use app\models\ClientUser;
use app\models\ContractorActivities;
use app\models\ContractorEducations;
use app\models\forms\FormClientAndRole;
use app\models\forms\FormClientCodeRegistration;
use app\models\forms\SingupContractorForm;
use app\models\forms\SingupExpertForm;
use app\services\MailerService;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\LoginForm;
use app\models\forms\SingupForm;
use app\models\User;
use app\models\ResetPasswordForm;
use app\models\forms\SendEmailForm;
use yii\helpers\Url;
use app\models\AccountActivation;
use yii\web\ErrorAction;
use yii\captcha\CaptchaAction;

class SiteController extends AppUserPartController
{
    public $layout = 'base';

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->user->isGuest) {

            $model_login = new LoginForm();
            $model_send_email = new SendEmailForm();
            $model_signup = new SingupForm();

            return $this->render('index', compact( 'model_login', 'model_send_email', 'model_signup'));
        }

        return $this->render('index', compact('user'));
    }


    /**
     * Страница регистрации пользователя
     *
     * @return array|string|Response
     */
    public function actionRegistration()
    {
        if (Yii::$app->user->isGuest) {

            $formClientCode = new FormClientCodeRegistration();

            if (Yii::$app->request->isAjax) {

                if (($code = $_POST['FormClientCodeRegistration']['code']) && $code !== '' && $clientCode = ClientCodes::findOne(['code' => $code])) {

                    $role = ClientCodeTypes::getUserRoleByType($clientCode->getType());
                    $client = $clientCode->client;
                    $admin = $client->settings->admin;

                    $clients = Client::findAllActiveClients();
                    $dataClients = ArrayHelper::map($clients, 'id', 'name');

                    $formRegistration = new SingupForm();
                    $formRegistration->setRole($role);
                    $formRegistration->setClientId($client->getId());

                    if ($role === User::ROLE_EXPERT) {

                        $formRegistration = new SingupExpertForm();
                        $formRegistration->setRole($role);
                        $formRegistration->setClientId($client->getId());

                        $response = [
                            'renderAjax' => $this->renderAjax('singup-expert', [
                                'formRegistration' => $formRegistration,
                                'dataClients' => $dataClients,
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if ($role === User::ROLE_CONTRACTOR) {

                        $formRegistration = new SingupContractorForm();
                        $formRegistration->setRole($role);
                        $formRegistration->setClientId($client->getId());
                        $contractorActivities = ContractorActivities::find()->all();

                        $response = [
                            'renderAjax' => $this->renderAjax('singup-contractor', [
                                'formRegistration' => $formRegistration,
                                'dataClients' => $dataClients,
                                'contractorActivities' => ArrayHelper::map($contractorActivities, 'id', 'title'),
                                'education' => new ContractorEducations()
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if (User::isUserMainAdmin($admin->getUsername())) {

                        $response = [
                            'renderAjax' => $this->renderAjax('singup', [
                                'formRegistration' => $formRegistration,
                                'dataClients' => $dataClients,
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;

                    }

                    if (in_array($role, [User::ROLE_USER, User::ROLE_ADMIN], true)) {

                        /** @var ClientRatesPlan $clientRatesPlan */
                        $clientRatesPlan = $client->findLastClientRatesPlan();
                        $ratesPlan = $clientRatesPlan->ratesPlan;

                        $countUsersCompany = User::find()
                            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                            ->andWhere(['client_user.client_id' => $client->getId()])
                            ->andWhere(['role' => $role])
                            ->andWhere(['!=', 'status', User::STATUS_NOT_ACTIVE])
                            ->count();

                        $maxCountUser = $role ===  User::ROLE_USER ? $ratesPlan->getMaxCountProjectUser() : $ratesPlan->getMaxCountTracker();
                        if ($maxCountUser > $countUsersCompany) {

                            $response = [
                                'renderAjax' => $this->renderAjax('singup', [
                                    'formRegistration' => $formRegistration,
                                    'dataClients' => $dataClients,
                                ]),
                            ];
                            Yii::$app->response->format = Response::FORMAT_JSON;
                            Yii::$app->response->data = $response;
                            return $response;

                        }

                        $response = ['errorMessage' => 'Превышено количество зарегистрированных пользователей по тарифу организации'];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    $response = [
                        'renderAjax' => $this->renderAjax('singup', [
                            'formRegistration' => $formRegistration,
                            'dataClients' => $dataClients,
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

                $response = ['errorMessage' => 'Указан некорректный код для регистрации'];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            return $this->render('registration', compact('formClientCode'));
        }

        return $this->redirect('/');
    }


    /**
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionSingup()
    {
        if (Yii::$app->request->isAjax && Yii::$app->user->isGuest) {

            $emailActivation = Yii::$app->params['emailActivation'];
            $model = $emailActivation ? new SingupForm(['scenario' => 'emailActivation']) : new SingupForm();
            if ($_POST['SingupExpertForm']) {
                $model = $emailActivation ? new SingupExpertForm(['scenario' => 'emailActivation']) : new SingupExpertForm();
            }
            if ($_POST['SingupContractorForm']) {
                $model = $emailActivation ? new SingupContractorForm(['scenario' => 'emailActivation']) : new SingupContractorForm();
            }

            if ($model->load(Yii::$app->request->post())) {

                $model->setUsername($model->getEmail());

                if ($model->validate()) {

                    $clientId = $model->getClientId();

                    if ($user = $model->singup()) {

                        if (ClientUser::createRecord($clientId, $user->getId())) {

                            if ($user->getConfirm() === User::NOT_CONFIRM) {

                                if ($model->sendActivationEmail($user)) {

                                    //Письмо с подтверждением отправлено
                                    $response = [
                                        'success_singup' => true,
                                        'message' => 'Спасибо за регистрацию. Письмо с подтверждением регистрации отправлено на указанный email.',
                                    ];
                                    Yii::$app->response->format = Response::FORMAT_JSON;
                                    Yii::$app->response->data = $response;
                                    return $response;
                                }

                                $user->clientUser->delete();
                                $user->delete();

                                //Письмо с подтверждением не отправлено
                                $response = [
                                    'error_singup_send_email' => true,
                                    'message' => ' - на указанный почтовый адрес не отправляются письма, возможно вы указали некорректный адрес;',
                                ];
                                Yii::$app->response->format = Response::FORMAT_JSON;
                                Yii::$app->response->data = $response;
                                return $response;
                            }
                        }
                    }
                } else {

                    $response = [
                        'error_uniq_email' => false,
                        'error_uniq_username' => false,
                        'error_match_username' => false,
                        'error_exist_agree' => false,
                    ];

                    if ($model->uniq_email === false) {
                        $response['error_uniq_email'] = true;
                    }

                    if ($model->exist_agree != 1) {
                        $response['error_exist_agree'] = true;
                    }

                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param $key
     * @return string|Response
     */
    public function actionActivateAccount($key)
    {
        $accountActivation = new AccountActivation($key);

        //Если ключ существует и не просрочен
        if ($accountActivation->isExist()) {
            //Если подтверждение регистрации прошло успешно
            if($accountActivation->activateAccount()) {

                //Отправка письма админу
                $user = $accountActivation->user;
                $user->sendEmailAdmin($user);
                return $this->redirect(Url::to(['/']));
            }

            //Ошибка подтверждения регистрации
            $model_login = new LoginForm();
            $model_send_email = new SendEmailForm();

            return $this->render('/site/activate-account', [
                'model_login' => $model_login,
                'model_send_email' => $model_send_email,
            ]);
        }

        //Если ключ не существует или просрочен
        $model_login = new LoginForm();
        $model_send_email = new SendEmailForm();

        return $this->render('/site/activate-account', [
            'model_login' => $model_login,
            'model_send_email' => $model_send_email,
        ]);
    }


    /**
     * @return array|bool
     * @throws Exception
     */
    public function actionLogin()
    {
        if (Yii::$app->request->isAjax && Yii::$app->user->isGuest) {

            $model = new LoginForm();

            if ($model->load(Yii::$app->request->post())) {

                $user = $model->getUser();

                //Если пользователь не подтвердил регистрацию и ввел верно пароль
                if ($user && $user->getConfirm() === User::NOT_CONFIRM && $user->validatePassword($model->getPassword()) === true) {

                    //Если ключ активации регистрационной ссылки просрочен, то отправить новое письмо
                    if ($user::isSecretKeyExpire($user->secret_key) < time()) {

                        $user->generateSecretKey();
                        $user->save();
                        if ($model->sendActivationEmail($user)) {

                            $response = [
                                'error_not_confirm_singup' => true,
                                'message' => 'Проверьте email, Вам отправлено новое письмо для подтверждения регистрации.'
                            ];
                            Yii::$app->response->format = Response::FORMAT_JSON;
                            Yii::$app->response->data = $response;
                            return $response;
                        }

                    } else {

                        $response = [
                            'error_not_confirm_singup' => true,
                            'message' => 'Проверьте email, Вам было отправлено письмо для подтверждения регистрации.'
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                } elseif ($model->login()) {

                    if (User::isUserMainAdmin($user->getUsername()) || User::isUserDev($user->getUsername()) || User::isUserManager($user->getUsername())) {

                        $response = ['url' => Url::to('/admin')];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if (User::isUserAdminCompany($user->getUsername())) {

                        $response = ['url' => Url::to('/client')];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if (User::isUserAdmin($user->getUsername())) {

                        $client = Client::findById($user->clientUser->getClientId());
                        if (User::isUserMainAdmin($client->settings->admin->getUsername())) {
                            $response = ['url' => Url::to('/admin')];
                        } else {
                            $response = ['url' => Url::to('/client')];
                        }
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if (User::isUserExpert($user->getUsername())) {

                        $response = ['url' => Url::to('/expert')];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if (User::isUserContractor($user->getUsername())) {

                        $response = ['url' => Url::to('/contractor')];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if (User::isUserSimple($user->getUsername())) {

                        $response = ['url' => Url::to('/')];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    if (($user->getConfirm() === User::CONFIRM) && ($user->getStatus() === User::STATUS_NOT_ACTIVE || $user->getStatus() === User::STATUS_DELETED)) {

                        $response = ['url' => Url::to('/')];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                } else {

                    //Если пара логин-пароль не существует
                    $response = ['error_not_user' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->redirect(['/']);
    }


    /**
     * @return array|bool
     * @throws Exception
     */
    public function actionSendEmail()
    {
        if(Yii::$app->request->isAjax) {

            $model = new SendEmailForm();

            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    if ($model->sendEmail()) {
                        //Если отправлено письмо
                        $response =  [
                            'success' => true,
                            'message' => [
                                'title' => 'Проверьте email',
                                'text' => 'На указанный адрес отправлено письмо со сслылкой для восстановления пароля.'
                            ],
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                    //Если письмо не отправлено письмо
                    $response =  [
                        'error' => true,
                        'message' => [
                            'title' => 'Запрос отменен',
                            'text' => 'Письмо на email не отправлено, указанный адрес не зарегистрирован.'
                        ],
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param $key
     * @return string|Response
     * @throws Exception
     */
    public function actionResetPassword($key)
    {

        $model = new ResetPasswordForm($key);

        //Если $key прошел проверку на валидность
        if ($model->exist === true) {

            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate() && $model->resetPassword()) {
                    //Если пароль изменен
                    return $this->redirect(['/']);
                }
            }

            return $this->render('reset-password', [
                'model' => $model,
            ]);

        }

        $model_send_email = new SendEmailForm();

        return $this->render('reset-password', [
            'model' => $model,
            'model_send_email' => $model_send_email,
        ]);
    }



    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        // Подключение шаблона администратора в пользовательской части
        if (!Yii::$app->user->isGuest) {
            if (User::isUserAdmin(Yii::$app->user->identity['username']) || User::isUserMainAdmin(Yii::$app->user->identity['username'])
                || User::isUserDev(Yii::$app->user->identity['username'])){
                $this->layout = '@app/modules/admin/views/layouts/base';
            }
        }

        return $this->render('about');
    }


    /**
     * @return \yii\console\Response|Response
     * @throws NotFoundHttpException
     */
    public function actionDownloadPresentation()
    {
        $file = DOCUMENTS_WEB . 'presentation/presentation_spaccel.pdf';

        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file, 'presentation_spaccel.pdf');
        }
        throw new NotFoundHttpException('Данный файл не найден');
    }


    /**
     * @return string
     */
    public function actionConfidentialityPolicy(): string
    {
        return $this->render('confidentiality-policy');
    }


    /**
     * @return string
     */
    public function actionMethodologicalGuide(): string
    {
        return $this->render('methodological-guide');
    }
}
