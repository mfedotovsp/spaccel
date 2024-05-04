<?php

namespace app\modules\contractor\controllers;

use app\models\ClientSettings;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\FormCreateProblem;
use app\models\forms\FormUpdateProblem;
use app\models\PatternHttpException;
use app\models\Problems;
use app\models\Projects;
use app\models\RespondsSegment;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use Yii;
use yii\base\ErrorException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProblemsController extends AppContractorController
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

        if ($action->id === 'task') {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('id'));
            if (!$task || $task->getType() !== StageExpertise::PROBLEM) {
                PatternHttpException::noData();
            }

            $contractor = User::findOne($task->getContractorId());
            if (!$contractor) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserSimple($currentUser->getUsername()) && $task->project->getUserId() === $currentUser->getId()) {
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
     * @param int $id
     * @return string
     */
    public function actionTask(int $id): string
    {
        $task = ContractorTasks::findOne($id);

        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $task->getProjectId()])
            ->one();

        $models = Problems::findAll([
            'project_id' => $project->getId(),
            'basic_confirm_id' => $task->getHypothesisId(),
            'contractor_id' => $task->getContractorId(),
            'task_id' => $task->getId()
        ]);

        /** @var $confirmSegment ConfirmSegment */
        $confirmSegment = ConfirmSegment::find(false)
            ->andWhere(['id' => $task->getHypothesisId()])
            ->one();

        /** @var $segment Segments */
        $segment = Segments::find(false)
            ->andWhere(['id' => $confirmSegment->getSegmentId()])
            ->one();

        $formModel = new FormCreateProblem($segment);

        $existTrashList = Problems::find(false)
            ->andWhere(['project_id' => $project->getId()])
            ->andWhere(['basic_confirm_id' => $task->getHypothesisId()])
            ->andWhere(['contractor_id' => $task->getContractorId()])
            ->andWhere(['task_id' => $task->getId()])
            ->andWhere(['not', ['deleted_at' => null]])
            ->exists();

        $trashList = Problems::find(false)
            ->andWhere(['project_id' => $project->getId()])
            ->andWhere(['basic_confirm_id' => $task->getHypothesisId()])
            ->andWhere(['contractor_id' => $task->getContractorId()])
            ->andWhere(['task_id' => $task->getId()])
            ->andWhere(['not', ['deleted_at' => null]])
            ->all();

        return $this->render('task', [
            'task' => $task,
            'project' => $project,
            'confirmSegment' => $confirmSegment,
            'segment' => $segment,
            'models' => $models,
            'existTrashList' => $existTrashList,
            'trashList' => $trashList,
            'formTaskComplete' => new FormTaskComplete(),
            'formModel' => $formModel,
        ]);
    }

    /**
     * @param int $id
     * @return array|false
     */
    public function actionList(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($id);
            $project = $task->project;

            $response = [
                'renderAjax' => $this->renderAjax('_index_ajax', [
                    'task' => $task,
                    'formTaskComplete' => new FormTaskComplete(),
                    'models' => Problems::findAll([
                        'project_id' => $project->getId(),
                        'basic_confirm_id' => $task->getHypothesisId(),
                        'contractor_id' => $task->getContractorId(),
                        'task_id' => $task->getId()
                    ])
                ])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionTrashList(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($id);
            $project = $task->project;

            $queryModels = Problems::find(false)
                ->andWhere(['project_id' => $project->getId()])
                ->andWhere(['contractor_id' => $task->getContractorId()])
                ->andWhere(['task_id' => $task->getId()])
                ->andWhere(['basic_confirm_id' => $task->getHypothesisId()])
                ->andWhere(['not', ['deleted_at' => null]]);

            $response = [
                'countItems' => $queryModels->count(),
                'renderAjax' => $this->renderAjax('_trash_ajax', [
                    'task' => $task,
                    'models' => $queryModels->all()
                ])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }

    /**
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function actionCreate(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($id);
            $confirmSegment = ConfirmSegment::findOne($task->getHypothesisId());
            $model = new FormCreateProblem($confirmSegment->hypothesis);
            $model->setContractorId(Yii::$app->user->getId());
            $model->setTaskId($task->getId());
            $model->setBasicConfirmId($confirmSegment->getId());

            if ($model->load(Yii::$app->request->post())) {
                if ($model->create()){

                    $response = [
                        'count' => Problems::find()
                            ->andWhere(['basic_confirm_id' => $task->getHypothesisId()])
                            ->andWhere(['contractor_id' => $task->getContractorId()])
                            ->andWhere(['task_id' => $task->getId()])
                            ->count(),
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                            'task' => $task,
                            'formTaskComplete' => new FormTaskComplete(),
                            'models' => Problems::findAll([
                                'basic_confirm_id' => $task->getHypothesisId(),
                                'contractor_id' => $task->getContractorId(),
                                'task_id' => $task->getId()
                            ]),
                        ]),
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
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionGetHypothesisToUpdate (int $id)
    {
        $model = $this->findModel($id);
        $formUpdate = new FormUpdateProblem($model);

        //Выбор респондентов, которые являются представителями сегмента
        $responds = RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $model->getConfirmSegmentId(), 'interview_confirm_segment.status' => '1'])->all();

        if(Yii::$app->request->isAjax) {

            $response = [
                'model' => $model,
                'renderAjax' => $this->renderAjax('update', [
                    'model' => $model,
                    'responds' => $responds,
                    'formUpdate' => $formUpdate
                ]),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            $form = new FormUpdateProblem($model);

            if ($form->load(Yii::$app->request->post())) {
                if ($form->update()) {
                    $response = [
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                            'task' => ContractorTasks::findOne($model->getTaskId()),
                            'formTaskComplete' => new FormTaskComplete(),
                            'models' => Problems::findAll([
                                'basic_confirm_id' => $model->getBasicConfirmId(),
                                'contractor_id' => $model->getContractorId(),
                                'task_id' => $model->getTaskId(),
                            ]),
                        ])
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
     * @param int $id
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDelete(int $id): bool
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->isAjax && $model->softDeleteStage()) {
            return true;
        }
        return false;
    }


    /**
     * @param int $id
     * @return void|Response
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionRecovery(int $id)
    {
        $model = $this->findModel($id, false);

        if($model->recoveryStage()) {
            return $this->redirect(['task', 'id' => $model->getTaskId()]);
        }

        PatternHttpException::noData();
    }


    /**
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return Problems|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): ?Problems
    {
        if (!$isOnlyNotDelete) {
            $model = Problems::find(false)
                ->andWhere(['id' => $id])
                ->one();

        } else {
            $model = Problems::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
