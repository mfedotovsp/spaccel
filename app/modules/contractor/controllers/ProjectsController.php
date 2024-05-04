<?php

namespace app\modules\contractor\controllers;

use app\models\ClientSettings;
use app\models\ContractorTasks;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\User;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

class ProjectsController extends AppContractorController
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

            $contractor = User::findOne((int)Yii::$app->request->get('id'));
            if (!$contractor) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $contractor->getId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $contractor->clientUser;

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
     * Получение проектов,
     * на которые был назначен исполнитель
     *
     * @param int $id
     * @return string
     */
    public function actionIndex(int $id): string
    {
        $user = User::findOne($id);
        // Проекты, на которые назначен исполнитель
        $projects = Projects::find(false)
            ->innerJoin('contractor_project', '`contractor_project`.`project_id` = `projects`.`id`')
            ->innerJoin('user', '`user`.`id` = `contractor_project`.`contractor_id`')
            ->andWhere(['user.id' => $user->getId()])
            ->orderBy(['contractor_project.created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'user' => $user,
            'projects' => $projects
        ]);
    }


    /**
     * Получение заданий по проекту
     *
     * @param int $contractorId
     * @param int $projectId
     * @return array|false
     */
    public function actionGetTasks(int $contractorId, int $projectId)
    {
        if (Yii::$app->request->isAjax) {

            $contractorTasks = ContractorTasks::find()
                ->andWhere(['contractor_id' => $contractorId, 'project_id' => $projectId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

            if (count($contractorTasks) > 0) {
                $response = [
                    'renderAjax' => $this->renderAjax('get-tasks', [
                        'tasks' => $contractorTasks
                    ]),
                ];
            } else {
                $response = [
                    'renderAjax' => $this->renderAjax('not-found-tasks'),
                ];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }
}
