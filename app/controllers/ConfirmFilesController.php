<?php

namespace app\controllers;

use app\models\ClientSettings;
use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\ConfirmFile;
use app\models\PatternHttpException;
use app\models\User;
use app\models\UserAccessToProjects;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ConfirmFilesController
 * @package app\controllers
 */
class ConfirmFilesController extends AppUserPartController
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

        if ($action->id === 'download') {
            $model = $this->findModel((int)Yii::$app->request->get('id'));
            $source = $model->source;
            $confirmDescription = $source->confirmDescription;
            $project = $confirmDescription->confirm->hypothesis->project;

            if (($project->getUserId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            if (User::isUserAdmin($currentUser->getUsername()) && $project->user->getIdAdmin() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $project->user->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE && !User::isUserAdminCompany($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }

                PatternHttpException::noAccess();
            }

            if (User::isUserExpert($currentUser->getUsername())) {

                $expert = User::findOne(Yii::$app->user->getId());

                /** @var UserAccessToProjects $userAccessToProject */
                $userAccessToProject = $expert->findUserAccessToProject($project->getId());

                if ($userAccessToProject) {

                    if ($userAccessToProject->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) {

                        $responsiveCommunication = $userAccessToProject->communication->responsiveCommunication;

                        if ($responsiveCommunication) {

                            if ($responsiveCommunication->communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) {
                                return parent::beforeAction($action);
                            }

                        } elseif (time() < $userAccessToProject->getDateStop()) {
                            return parent::beforeAction($action);
                        }
                        PatternHttpException::noAccess();

                    } elseif ($userAccessToProject->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) {

                        return parent::beforeAction($action);

                    } else {
                        PatternHttpException::noAccess();
                    }
                } else{
                    PatternHttpException::noAccess();
                }

            } else{
                PatternHttpException::noAccess();
            }

        } elseif ($action->id === 'delete') {

            $model = $this->findModel((int)Yii::$app->request->get('id'));
            $source = $model->source;
            $confirmDescription = $source->confirmDescription;
            $project = $confirmDescription->confirm->hypothesis->project;

            if (($project->getUserId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else {
            return parent::beforeAction($action);
        }
    }

    /**
     * @param int $id
     * @return \yii\console\Response|Response
     * @throws NotFoundHttpException
     */
    public function actionDownload(int $id)
    {
        $model = $this->findModel($id);
        $source = $model->source;
        $confirmDescription = $source->confirmDescription;
        $project = $confirmDescription->confirm->hypothesis->project;

        $path = UPLOAD.'/user-'.$project->user->getId().'/project-'.$project->getId().'/confirm_files/type-'.
            $confirmDescription->getType().'/source-'.$source->getId().'/';
        $file = $path . $model->getServerFile();

        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file, $model->getFileName());
        }
        throw new NotFoundHttpException('Данный файл не найден');
    }

    /**
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(int $id): array
    {
        $model = $this->findModel($id);
        $source = $model->source;
        $confirmDescription = $source->confirmDescription;
        $project = $confirmDescription->confirm->hypothesis->project;

        $path = UPLOAD.'/user-'.$project->user->getId().'/project-'.$project->getId().'/confirm_files/type-'.
            $confirmDescription->getType().'/source-'.$source->getId().'/';
        $file = $path . $model->getServerFile();

        if (Yii::$app->request->isAjax) {
            if (unlink($file) && $model->delete()) {
                $response = ['success' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        throw new NotFoundHttpException('Данный файл не найден');
    }



    /**
     * @param int $id
     * @return ConfirmFile
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): ConfirmFile
    {
        $model = ConfirmFile::findOne($id);
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Not found file');
    }

}
