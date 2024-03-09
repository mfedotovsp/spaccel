<?php

namespace app\modules\admin\controllers;

use app\models\ClientSettings;
use app\models\forms\AvatarForm;
use app\models\forms\PasswordChangeForm;
use app\models\forms\ProfileForm;
use app\models\PatternHttpException;
use app\models\Projects;
use Throwable;
use Yii;
use app\models\User;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class ProfileController extends AppAdminController
{

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());
        $currentClientUser = $currentUser->clientUser;

        if ($action->id === 'index') {

            $admin = User::findOne((int)Yii::$app->request->get('id'));
            if (!$admin) {
                PatternHttpException::noData();
            }

            $adminClientUser = $admin->clientUser;

            if ($currentClientUser->getClientId() === $adminClientUser->getClientId() && ($admin->getId() === $currentUser->getId() || User::isUserDev(Yii::$app->user->identity['username']) || User::isUserMainAdmin(Yii::$app->user->identity['username']))) {
                return parent::beforeAction($action);
            }

            if ($adminClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE
                && (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()))) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }else{
            return parent::beforeAction($action);
        }
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionIndex(int $id): string
    {
        $user = User::findOne($id);
        $count_users = User::find()->andWhere(['id_admin' => $id])->count();
        $countProjects = Projects::find()->with('user')
            ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
            ->andWhere(['user.id_admin' => $id])->count();
        $profile = new ProfileForm($id);
        $passwordChangeForm = new PasswordChangeForm($user);
        $avatarForm = new AvatarForm($id);

        return $this->render('index', [
            'user' => $user,
            'count_users' => $count_users,
            'countProjects' => $countProjects,
            'profile' => $profile,
            'passwordChangeForm' => $passwordChangeForm,
            'avatarForm' => $avatarForm,
        ]);
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetUserIsOnline(int $id)
    {
        if (Yii::$app->request->isAjax) {

        $user = User::findOne($id);

            if ($user->checkOnline === true) {

                $response = ['user_online' => true, 'message' => 'Пользователь сейчас Online'];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            if (is_string($user->checkOnline)) {

                $response = ['user_logout' => true, 'message' => 'Пользователь был в сети ' . $user->checkOnline];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionUpdateProfile(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $model = new ProfileForm($id);
            $count_users = User::find()->andWhere(['id_admin' => $id])->count();
            $countProjects = Projects::find()->with('user')
                ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                ->andWhere(['user.id_admin' => $id])->count();

            if ($model->load(Yii::$app->request->post())) {

                if ($model->validate()) {

                    if ($model->update()){

                        if ($model->checking_mail_sending) {

                            $user = User::findOne($id);

                            $response = [
                                'success' => true, 'user' => User::findOne($id),
                                'renderAjax' => $this->renderAjax('ajax_data_profile', [
                                    'user' => $user, 'count_users' => $count_users, 'countProjects' => $countProjects, 'profile' => new ProfileForm($id),
                                    'passwordChangeForm' => new PasswordChangeForm($user), 'avatarForm' => new AvatarForm($id),
                                ]),
                            ];
                            Yii::$app->response->format = Response::FORMAT_JSON;
                            Yii::$app->response->data = $response;
                            return $response;
                        }

                        //Письмо с уведомлением не отправлено
                        $response = ['error_send_email' => true];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                } else {

                    $response = [
                        'error_uniq_email' => false,
                        'error_match_username' => false,
                        'error_uniq_username' => false,
                    ];

                    if ($model->uniq_email === false) {
                        $response['error_uniq_email'] = true;
                    }

                    if ($model->uniq_username === false) {
                        $response['error_uniq_username'] = true;
                    }

                    if ($model->match_username === false) {
                        $response['error_match_username'] = true;
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
     * @param int $id
     * @return bool|array
     * @throws Exception
     */
    public function actionChangePassword(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $user = User::findOne($id);
            $model = new PasswordChangeForm($user);

            if ($model->load(Yii::$app->request->post())){

                if ($model->validate()) {
                    if ($model->changePassword()) {

                        $response = ['success' => true];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                } elseif (!$model->validate(['currentPassword'])) {

                    $response = ['errorCurrentPassword' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|false
     * @throws Exception
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionLoadAvatarImage(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $avatarForm = new AvatarForm($id);
            $count_users = User::find()->andWhere(['id_admin' => $id])->count();
            $countProjects = Projects::find()->with('user')
                ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                ->andWhere(['user.id_admin' => $id])->count();

            if (isset($_POST['imageMin'])) {

                if ($avatarForm->loadMinImage()) {

                    $user = User::findOne($id);

                    $response = [
                        'success' => true, 'user' => $user,
                        'renderAjax' => $this->renderAjax('ajax_data_profile', [
                            'user' => $user, 'count_users' => $count_users, 'countProjects' => $countProjects, 'profile' => new ProfileForm($id),
                            'passwordChangeForm' => new PasswordChangeForm($user), 'avatarForm' => new AvatarForm($id),
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

            } else {

                if ($result = $avatarForm->loadMaxImage()) {

                    $response = $result;
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
                return false;
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetDataAvatar(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $user = User::findOne($id);

            $response = ['path_max' => '/upload/user-' . $user->getId() . '/avatar/' . $user->getAvatarMaxImage()];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;

        }
        return false;
    }


    /**
     * @param int $id
     * @return bool
     */
    public function actionDeleteUnusedImage(int $id): bool
    {
        if (Yii::$app->request->isAjax) {
            $avatarForm = new AvatarForm($id);
            if (isset($_POST['imageMax']) && $avatarForm->deleteUnusedImage()) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionDeleteAvatar(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $avatarForm = new AvatarForm($id);
            $count_users = User::find()->andWhere(['id_admin' => $id])->count();
            $countProjects = Projects::find()->with('user')
                ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                ->andWhere(['user.id_admin' => $id])->count();

            if ($avatarForm->deleteOldAvatarImages()) {

                $user = User::findOne($id);

                $response = [
                    'success' => true, 'user' => $user,
                    'renderAjax' => $this->renderAjax('ajax_data_profile', [
                        'user' => $user, 'count_users' => $count_users, 'countProjects' => $countProjects, 'profile' => new ProfileForm($id),
                        'passwordChangeForm' => new PasswordChangeForm($user), 'avatarForm' => new AvatarForm($id),
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return User|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): ?User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
