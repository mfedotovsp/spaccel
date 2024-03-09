<?php

namespace app\modules\contractor\controllers;

use app\models\ConfirmProblem;
use app\models\ContractorTasks;
use app\models\forms\FormCreateGcp;
use app\models\Gcps;
use app\models\PatternHttpException;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use Yii;
use yii\base\ErrorException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class GcpsController extends AppContractorController
{

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'task') {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('id'));
            if (!$task || $task->getType() !== StageExpertise::GCP) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserSimple($currentUser->getUsername()) && $task->project->getUserId() === $currentUser->getId()) {
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
    public function actionTask(int $id): string
    {
        $task = ContractorTasks::findOne($id);

        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $task->getProjectId()])
            ->one();

        $models = Gcps::findAll([
            'project_id' => $project->getId(),
            'basic_confirm_id' => $task->getHypothesisId(),
            'contractor_id' => $task->getContractorId(),
            'task_id' => $task->getId()
        ]);

        /** @var $confirmProblem ConfirmProblem */
        $confirmProblem = ConfirmProblem::find(false)
            ->andWhere(['id' => $task->getHypothesisId()])
            ->one();

        /** @var $problem Problems */
        $problem = Problems::find(false)
            ->andWhere(['id' => $confirmProblem->getProblemId()])
            ->one();

        $existTrashList = Gcps::find(false)
            ->andWhere(['project_id' => $project->getId()])
            ->andWhere(['basic_confirm_id' => $task->getHypothesisId()])
            ->andWhere(['contractor_id' => $task->getContractorId()])
            ->andWhere(['task_id' => $task->getId()])
            ->andWhere(['not', ['deleted_at' => null]])
            ->exists();

        $trashList = Gcps::find(false)
            ->andWhere(['project_id' => $project->getId()])
            ->andWhere(['basic_confirm_id' => $task->getHypothesisId()])
            ->andWhere(['contractor_id' => $task->getContractorId()])
            ->andWhere(['task_id' => $task->getId()])
            ->andWhere(['not', ['deleted_at' => null]])
            ->all();

        $segment = Segments::find(false)
            ->andWhere(['id' => $problem->getSegmentId()])
            ->one();

        return $this->render('task', [
            'task' => $task,
            'project' => $project,
            'confirmProblem' => $confirmProblem,
            'problem' => $problem,
            'segment' => $segment,
            'models' => $models,
            'existTrashList' => $existTrashList,
            'trashList' => $trashList,
            'formTaskComplete' => new FormTaskComplete(),
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
                    'models' => Gcps::findAll([
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

            $queryModels = Gcps::find(false)
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
            $confirmProblem = ConfirmProblem::findOne($task->getHypothesisId());
            $model = new FormCreateGcp($confirmProblem->hypothesis);
            $model->setContractorId(Yii::$app->user->getId());
            $model->setTaskId($task->getId());
            $model->setBasicConfirmId($confirmProblem->getId());

            if ($model->load(Yii::$app->request->post())) {
                if ($model->create()) {

                    $response = [
                        'count' => Gcps::find()
                            ->andWhere(['basic_confirm_id' => $task->getHypothesisId()])
                            ->andWhere(['contractor_id' => $task->getContractorId()])
                            ->andWhere(['task_id' => $task->getId()])
                            ->count(),
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                            'task' => $task,
                            'formTaskComplete' => new FormTaskComplete(),
                            'models' => Gcps::findAll([
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
    public function actionGetHypothesisToUpdate(int $id)
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->isAjax) {

            $response = [
                'model' => $model,
                'renderAjax' => $this->renderAjax('update', ['model' => $model]),
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
        if(Yii::$app->request->isAjax) {

            $model = $this->findModel($id);

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()){

                    $response = [
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                            'task' => ContractorTasks::findOne($model->getTaskId()),
                            'formTaskComplete' => new FormTaskComplete(),
                            'models' => Gcps::findAll([
                                'basic_confirm_id' => $model->getBasicConfirmId(),
                                'contractor_id' => $model->getContractorId(),
                                'task_id' => $model->getTaskId(),
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
     * @return Gcps|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): ?Gcps
    {
        if (!$isOnlyNotDelete) {
            $model = Gcps::find(false)
                ->andWhere(['id' => $id])
                ->one();

        } else {
            $model = Gcps::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}