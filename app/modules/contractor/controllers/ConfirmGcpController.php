<?php

namespace app\modules\contractor\controllers;

use app\models\ConfirmGcp;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\FormCreateMvp;
use app\models\forms\SearchForm;
use app\models\Gcps;
use app\models\Problems;
use app\models\Projects;
use app\models\QuestionsConfirmGcp;
use app\models\RespondsGcp;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\StatusConfirmHypothesis;
use yii\web\HttpException;
use app\models\PatternHttpException;
use app\models\User;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ConfirmGcpController extends AppContractorController
{

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if (in_array($action->id, ['task', 'view-trash'], true)) {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('id'));
            if (!$task || $task->getType() !== StageExpertise::CONFIRM_GCP) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserSimple($currentUser->getUsername()) && $task->project->getUserId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        } else{
            return parent::beforeAction($action);
        }
    }


    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionTask(int $id)
    {
        $task = ContractorTasks::findOne($id);
        $model = $this->findModel($task->getHypothesisId(), false);

        if ($model->getDeletedAt()) {
            return $this->redirect(['view-trash', 'id' => $id]);
        }

        $gcp = Gcps::findOne($model->getGcpId());
        $problem = Problems::findOne($gcp->getProblemId());
        $segment = Segments::findOne($gcp->getSegmentId());
        $project = Projects::findOne($gcp->getProjectId());
        $questions = QuestionsConfirmGcp::findAll(['confirm_id' => $task->getHypothesisId()]);

        if ($task->activity->getTitle() === 'Маркетинг') {
            return $this->render('task_marketing', [
                'task' => $task,
                'model' => $model,
                'gcp' => $gcp,
                'problem' => $problem,
                'segment' => $segment,
                'project' => $project,
                'questions' => $questions,
                'searchForm' => new SearchForm()
            ]);
        }

        return $this->render('task', [
            'task' => $task,
            'model' => $model,
            'gcp' => $gcp,
            'problem' => $problem,
            'segment' => $segment,
            'project' => $project,
            'questions' => $questions,
            'searchForm' => new SearchForm()
        ]);
    }


    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewTrash(int $id): string
    {
        $task = ContractorTasks::findOne($id);
        $model = $this->findModel($task->getHypothesisId(), false);

        /** @var Gcps $gcp */
        $gcp = Gcps::find(false)
            ->andWhere(['id' => $model->getGcpId()])
            ->one();

        /** @var Problems $problem */
        $problem = Problems::find(false)
            ->andWhere(['id' => $gcp->getProblemId()])
            ->one();

        /** @var Segments $segment */
        $segment = Segments::find(false)
            ->andWhere(['id' => $gcp->getSegmentId()])
            ->one();

        /** @var Projects $project */
        $project = Projects::find(false)
            ->andWhere(['id' => $gcp->getProjectId()])
            ->one();

        /** @var QuestionsConfirmGcp[] $questions */
        $questions = QuestionsConfirmGcp::find(false)
            ->andWhere(['confirm_id' => $task->getHypothesisId()])
            ->all();

        if ($task->activity->getTitle() === 'Маркетинг') {
            return $this->render('task_marketing', [
                'task' => $task,
                'model' => $model,
                'gcp' => $gcp,
                'problem' => $problem,
                'segment' => $segment,
                'project' => $project,
                'questions' => $questions,
                'searchForm' => new SearchForm()
            ]);
        }

        return $this->render('task', [
            'task' => $task,
            'model' => $model,
            'gcp' => $gcp,
            'problem' => $problem,
            'segment' => $segment,
            'project' => $project,
            'questions' => $questions,
            'searchForm' => new SearchForm()
        ]);
    }


    /**
     * Проверка данных подтверждения на этапе разработки MVP
     *
     * @param int $id
     * @return array|bool
     */
    public function actionDataAvailabilityForNextStep(int $id)
    {
        $task = ContractorTasks::findOne($id);
        $model = ConfirmGcp::findOne($task->getHypothesisId());
        $formCreateMvp = new FormCreateMvp($model->hypothesis);

        $count_descInterview = (int)RespondsGcp::find()->with('interview')
            ->leftJoin('interview_confirm_gcp', '`interview_confirm_gcp`.`respond_id` = `responds_gcp`.`id`')
            ->andWhere(['confirm_id' => $model->getId()])->andWhere(['not', ['interview_confirm_gcp.id' => null]])->count();

        $count_positive = (int)RespondsGcp::find()->with('interview')
            ->leftJoin('interview_confirm_gcp', '`interview_confirm_gcp`.`respond_id` = `responds_gcp`.`id`')
            ->andWhere(['confirm_id' => $model->getId(), 'interview_confirm_gcp.status' => '1'])->count();

        if(Yii::$app->request->isAjax) {

            if (($model->mvps && $model->getCountPositive() <= $count_positive && $model->gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) || (count($model->responds) === $count_descInterview && $model->getCountPositive() <= $count_positive && $model->gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED)) {

                $response =  [
                    'success' => true,
                    'renderAjax' => $this->renderAjax('/mvps/create', [
                        'confirmGcp' => $model,
                        'model' => $formCreateMvp,
                        'task' => $task,
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            $response = ['error' => true];
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
    public function actionShowDataConfirmHypothesis(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($id);
            /** @var $model ConfirmGcp */
            $model = ConfirmGcp::find(false)
                ->andWhere(['id' => $task->getHypothesisId()])
                ->one();

            /** @var $gcp Gcps */
            $gcp = Gcps::find(false)
                ->andWhere(['id' => $model->getGcpId()])
                ->one();

            /** @var $problem Problems */
            $problem = Problems::find(false)
                ->andWhere(['id' => $gcp->getProblemId()])
                ->one();

            /** @var $segment ConfirmSegment */
            $confirmSegment = ConfirmSegment::find(false)
                ->andWhere(['segment_id' => $gcp->getSegmentId()])
                ->one();

            /** @var $segment Segments */
            $segment = Segments::find(false)
                ->andWhere(['id' => $gcp->getSegmentId()])
                ->one();

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $gcp->getProjectId()])
                ->one();

            /** @var $questions QuestionsConfirmGcp[] */
            $questions = QuestionsConfirmGcp::find(false)
                ->andWhere(['confirm_id' => $model->getId()])
                ->all();

            $countContractorResponds = (int)RespondsGcp::find()
                ->andWhere(['not', ['contractor_id' => null]])
                ->andWhere(['confirm_id' => $model->getId()])
                ->count();

            $response = [
                'renderAjax' => $this->renderAjax('data-confirm-hypothesis', [
                    'isTaskMarketing' => $task->activity->getTitle() === 'Маркетинг',
                    'model' => $model,
                    'gcp' => $gcp,
                    'problem' => $problem,
                    'confirmSegment' => $confirmSegment,
                    'segment' => $segment,
                    'project' => $project,
                    'questions' => $questions,
                    'countContractorResponds' => $countContractorResponds
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
     * @param bool $isOnlyNotDelete
     * @return ConfirmGcp|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): ?ConfirmGcp
    {
        if (!$isOnlyNotDelete) {
            $model = ConfirmGcp::find(false)
                ->andWhere(['id' => $id])
                ->one();
        } else {
            $model = ConfirmGcp::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}