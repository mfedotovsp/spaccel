<?php

namespace app\controllers;

use app\models\ClientSettings;
use app\models\forms\AvatarForm;
use app\models\forms\PasswordChangeForm;
use app\models\forms\ProfileForm;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\Roadmap;
use app\models\Segments;
use Throwable;
use Yii;
use app\models\User;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер для получения и редактирования информации из профиля проектанта
 *
 * Class ProfileController
 * @package app\controllers
 */
class ProfileController extends AppUserPartController
{
    public $layout = 'profile';


    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());
        $currentClientUser = $currentUser->clientUser;

        if (in_array($action->id, ['index', 'result', 'roadmap', 'report', 'presentation'])) {

            $user = User::findOne((int)Yii::$app->request->get('id'));
            if (!$user) {
                PatternHttpException::noData();
            }

            if ($currentUser->getId() === $user->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserAdmin($currentUser->getUsername()) && $user->getIdAdmin() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $user->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE && !User::isUserAdminCompany($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }
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
        $profile = new ProfileForm($id);
        $passwordChangeForm = new PasswordChangeForm($user);
        $avatarForm = new AvatarForm($id);

        return $this->render('index', [
            'user' => $user,
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
        $user = User::findOne($id);

        if (Yii::$app->request->isAjax) {

            if ($user->checkOnline === true) {

                $response = ['user_online' => true, 'message' => 'Пользователь сейчас Online'];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            $response = ['user_logout' => true, 'message' => 'Пользователь был в сети ' . $user->checkOnline];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;

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
            if ($model->load(Yii::$app->request->post())){
                if ($model->validate()) {
                    if ($model->update()){
                        if ($model->checking_mail_sending) {
                            $response = ['success' => true, 'user' => User::findOne($id)];
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
                        'error_uniq_username' => false,
                        'error_match_username' => false,
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
     * @return bool[]|false|string[]
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

                    $response = ['errorCurrentPassword' => 'true'];
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
     * @return array|bool
     * @throws Throwable
     * @throws Exception
     * @throws StaleObjectException
     */
    public function actionLoadAvatarImage(int $id)
    {
        $avatarForm = new AvatarForm($id);

        if (Yii::$app->request->isAjax) {

            if (isset($_POST['imageMin'])) {

                if ($avatarForm->loadMinImage()) {

                    $user = User::findOne($id);

                    $response = [
                        'success' => true, 'user' => $user,
                        'renderAjax' => $this->renderAjax('ajax_data_profile', [
                            'user' => $user, 'profile' => new ProfileForm($id),
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
        $user = User::findOne($id);

        if (Yii::$app->request->isAjax) {

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

            if ($avatarForm->deleteOldAvatarImages()) {

                $user = User::findOne($id);

                $response = [
                    'success' => true, 'user' => $user,
                    'renderAjax' => $this->renderAjax('ajax_data_profile', [
                        'user' => $user, 'profile' => new ProfileForm($id),
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
     * @return string
     */
    public function actionResult(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('result', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetDataProjects(int $id)
    {
        $projects = Projects::findAll(['user_id' => $id]);

        if(Yii::$app->request->isAjax) {

            $response = ['projects' => $projects];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetResultProject(int $id)
    {
        $project = Projects::findOne($id);

        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('_result_ajax', ['project' => $project])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionRoadmap(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('roadmap', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetRoadmapProject(int $id)
    {
        $project = Projects::findOne($id);
        $roadmaps = [];

        foreach ($project->segments as $i => $segment){
            $roadmaps[$i] = new Roadmap($segment->getId());
        }

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('_roadmap_ajax', ['roadmaps' => $roadmaps]),
                'project' => $project,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionReport(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('report', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    /**
     * @param $id
     * @return array|bool
     */
    public function actionGetReportProject ($id)
    {
        $project = Projects::findOne($id);
        $segments = Segments::findAll(['project_id' => $id]);

        foreach ($segments as $s => $segment) {

            $segment->propertyContainer->addProperty('title', 'Сегмент ' . ($s+1));

            foreach ($segment->problems as $p => $problem) {

                $problem->propertyContainer->addProperty('title', 'ГПС ' . ($s+1) . '.' . ($p+1));

                foreach ($problem->gcps as $g => $gcp) {

                    $gcp->propertyContainer->addProperty('title', 'ГЦП ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1));

                    foreach ($gcp->mvps as $m => $mvp) {

                        $mvp->propertyContainer->addProperty('title', 'MVP ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1) . '.' . ($m+1));
                    }
                }
            }
        }

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('/projects/report', ['segments' => $segments]),
                'project' => $project,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionPresentation(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('presentation', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    /**
     * @param $id
     * @return array|bool
     */
    public function actionGetPresentationProject ($id)
    {
        $project = Projects::findOne($id);

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('_presentation_ajax', ['project' => $project]),
                'project' => $project,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
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
