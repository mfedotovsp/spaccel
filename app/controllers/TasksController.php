<?php

namespace app\controllers;

use app\models\ContractorActivities;
use app\models\ContractorProject;
use app\models\ContractorTasks;
use app\models\forms\FormComment;
use app\models\forms\FormCreateTaskHypothesis;
use app\models\PatternHttpException;
use app\models\StageExpertise;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\Response;

class TasksController extends AppUserPartController
{
    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());
        if (!User::isUserSimple($currentUser->getUsername())) {
            PatternHttpException::noAccess();
        }

        return parent::beforeAction($action);
    }

    /**
     * Получить форму создания
     * задания для исполнителя
     * на этапе проекта
     *
     * @param int $projectId
     * @param int $stage
     * @param int $stageId
     * @param int|null $contractorId
     * @return array|bool
     */
    public function actionGetTaskCreate(int $projectId, int $stage, int $stageId, int $contractorId = null)
    {
        if(Yii::$app->request->isAjax) {

            $contractorProjects = ContractorProject::findAll(['project_id' => $projectId, 'deleted_at' => null]);
            $contractorIds = array_unique(array_column($contractorProjects, 'contractor_id'));

            if (count($contractorIds) > 0) {

                /** @var $activitiesForStage ContractorActivities[] */
                $activitiesForStage = [];
                if (in_array($stage, [StageExpertise::SEGMENT, StageExpertise::PROBLEM, StageExpertise::GCP, StageExpertise::MVP], true)) {
                    if ($stage === StageExpertise::GCP) {
                        $activitiesForStage = ContractorActivities::find()->andWhere(['in', 'title', ['Маркетинг', 'Техническая разработка']])->all();
                    } else {
                        $activitiesForStage = ContractorActivities::findAll(['title' => 'Маркетинг']);
                    }

                } elseif (in_array($stage, [StageExpertise::CONFIRM_SEGMENT, StageExpertise::CONFIRM_PROBLEM, StageExpertise::CONFIRM_GCP, StageExpertise::CONFIRM_MVP], true)) {
                    if ($stage === StageExpertise::CONFIRM_MVP) {
                        $activitiesForStage = ContractorActivities::findAll(['title' => 'Полевая работа']);
                    } else {
                        $activitiesForStage = ContractorActivities::find()->andWhere(['in', 'title', ['Маркетинг', 'Полевая работа']])->all();
                    }
                }

                if (!$activitiesForStage) {
                    $response = [
                        'headerContent' => 'Новое задание',
                        'renderAjax' => $this->renderAjax('not-found-activities'),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

                $contractorIdsToActivities = [];
                foreach ($contractorProjects as $contractorProject) {
                    if (!in_array($contractorProject->getContractorId(), $contractorIdsToActivities, true)) {
                        foreach ($activitiesForStage as $activity) {
                            if ($contractorProject->getActivityId() === $activity->getId()) {
                                $contractorIdsToActivities[] = $contractorProject->getContractorId();
                                break;
                            }
                        }
                    }
                }

                $contractorsQuery = User::find()
                    ->innerJoin('contractor_info', '`contractor_info`.`contractor_id` = `user`.`id`')
                    ->andWhere(['in', 'user.id', $contractorIdsToActivities]);

                if ((int)$contractorsQuery->count() === 0) {
                    $response = [
                        'headerContent' => 'Новое задание',
                        'renderAjax' => $this->renderAjax('not-found-contractors'),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

                $activitiesIdsForStage = array_map(static function (ContractorActivities $activity) {
                    return $activity->getId();
                }, $activitiesForStage);

                /** @var $contractors User[] */
                $contractors = $contractorsQuery
                    ->select('user.*')
                    ->all();

                $contractorIds = array_unique(array_column($contractors, 'id'));
                $contractorOptions = ArrayHelper::map($contractors, 'id', 'username');
                $activitiesContractor = ContractorProject::findAll([
                    'contractor_id' => $contractorId ?: $contractorIds[0],
                    'project_id' => $projectId,
                    'deleted_at' => null
                ]);

                $activityIds = array_unique(array_column($activitiesContractor, 'activity_id'));
                $activityIds = array_intersect($activityIds, $activitiesIdsForStage);
                $activities = ContractorActivities::find()->andWhere(['in', 'id', $activityIds])->all();
                $activityOptions = ArrayHelper::map($activities, 'id', 'title');

                $formTask = new FormCreateTaskHypothesis($projectId, $stage, $stageId);
                $formTask->setContractorId($contractorId ?: $contractorIds[0]);
                $formTask->setActivityId($activityIds[0]);

                $response = [
                    'headerContent' => 'Новое задание',
                    'renderAjax' => $this->renderAjax('create', [
                        'formTask' => $formTask,
                        'contractorOptions' => $contractorOptions,
                        'activityOptions' => $activityOptions
                    ]),
                ];

            } else {
                $response = [
                    'headerContent' => 'Новое задание',
                    'renderAjax' => $this->renderAjax('not-found-contractors'),
                ];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * Сохранение формы с новым заданием
     * для исполнителя на этапе проекта
     *
     * @return array|false
     */
    public function actionCreate()
    {
        if(Yii::$app->request->isAjax) {

            $success = false;
            $reloadPage = false;
            if ($_POST['FormCreateTaskHypothesis']) {
                $projectId = $_POST['FormCreateTaskHypothesis']['projectId'];
                $stage = $_POST['FormCreateTaskHypothesis']['type'];
                $stageId = $_POST['FormCreateTaskHypothesis']['hypothesisId'];
                $formTask = new FormCreateTaskHypothesis($projectId, $stage, $stageId);
                if ($formTask->load(Yii::$app->request->post())) {
                    [$success, $reloadPage] = $formTask->create();
                }
            }

            $response = [
                'reloadPage' => $reloadPage,
                'renderAjax' => $this->renderAjax('result-create', [
                    'success' => (bool)$success]),
            ];

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * Получить задания
     * на этапе проекта
     *
     * @param int $projectId
     * @param int $stage
     * @param int $stageId
     * @return array|bool
     */
    public function actionGetTasks(int $projectId, int $stage, int $stageId)
    {
        if (Yii::$app->request->isAjax) {

            $contractorTasks = ContractorTasks::findAll([
                'project_id' => $projectId,
                'type' => $stage,
                'hypothesis_id' => $stageId
            ]);

            if (count($contractorTasks) > 0) {

                $response = [
                    'headerContent' => 'Список заданий',
                    'renderAjax' => $this->renderAjax('get-tasks', [
                        'tasks' => $contractorTasks
                    ]),
                ];

            } else {
                $response = [
                    'headerContent' => 'Список заданий',
                    'renderAjax' => $this->renderAjax('not-found-tasks'),
                ];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * Получить задания по проекту и исполнителю
     *
     * @param int $contractorId
     * @param int $projectId
     * @return array|false
     */
    public function actionGetTasksByParams(int $contractorId, int $projectId)
    {
        if (Yii::$app->request->isAjax) {

            /** @var $contractorTasks ContractorTasks[] */
            $contractorTasks = ContractorTasks::find()
                ->andWhere(['project_id' => $projectId, 'contractor_id' => $contractorId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

            if (count($contractorTasks) > 0) {

                $response = [
                    'renderAjax' => $this->renderAjax('get-tasks-by-params', [
                        'tasks' => $contractorTasks, 'formComment' => new FormComment()
                    ]),
                ];

            } else {
                $response = [
                    'renderAjax' => $this->renderAjax('not-found-tasks-by-contractor'),
                ];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $taskId
     * @param int $newStatus
     * @return array|false
     * @throws \Throwable
     */
    public function actionChangeStatus(int $taskId, int $newStatus)
    {
        if (Yii::$app->request->isAjax) {
            $response = ['success' => false];
            $formComment = new FormComment();
            $task = ContractorTasks::findOne($taskId);
            if ($formComment->load(Yii::$app->request->post()) && $task->changeStatus($newStatus, $formComment->getComment())) {
                $response = ['success' => true, 'projectId' => $task->getProjectId()];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }
}
