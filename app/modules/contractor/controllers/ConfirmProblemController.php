<?php

namespace app\modules\contractor\controllers;

use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\FormCreateGcp;
use app\models\forms\SearchForm;
use app\models\PatternHttpException;
use app\models\Problems;
use app\models\Projects;
use app\models\QuestionsConfirmProblem;
use app\models\RespondsProblem;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ConfirmProblemController extends AppContractorController
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
            if (!$task || $task->getType() !== StageExpertise::CONFIRM_PROBLEM) {
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

        $problem = Problems::findOne($model->getProblemId());
        $segment = Segments::findOne($problem->getSegmentId());
        $project = Projects::findOne($problem->getProjectId());
        $questions = QuestionsConfirmProblem::findAll(['confirm_id' => $task->getHypothesisId()]);

        if ($task->activity->getTitle() === 'Маркетинг') {

            return $this->render('task_marketing', [
                'task' => $task,
                'model' => $model,
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

        /** @var Problems $problem */
        $problem = Problems::find(false)
            ->andWhere(['id' => $model->getProblemId()])
            ->one();

        /** @var Segments $segment */
        $segment = Segments::find(false)
            ->andWhere(['id' => $problem->getSegmentId()])
            ->one();

        /** @var Projects $project */
        $project = Projects::find(false)
            ->andWhere(['id' => $problem->getProjectId()])
            ->one();

        /** @var QuestionsConfirmProblem[] $questions */
        $questions = QuestionsConfirmProblem::find(false)
            ->andWhere(['confirm_id' => $task->getHypothesisId()])
            ->all();

        if ($task->activity->getTitle() === 'Маркетинг') {
            return $this->render('task_marketing', [
                'task' => $task,
                'model' => $model,
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
            'problem' => $problem,
            'segment' => $segment,
            'project' => $project,
            'questions' => $questions,
            'searchForm' => new SearchForm()
        ]);
    }


    /**
     * Проверка данных подтверждения на этапе разработки ГЦП
     *
     * @param int $id
     * @return array|bool
     */
    public function actionDataAvailabilityForNextStep(int $id)
    {
        $task = ContractorTasks::findOne($id);
        $model = ConfirmProblem::findOne($task->getHypothesisId());
        $formCreateGcp = new FormCreateGcp($model->hypothesis);

        $count_descInterview = (int)RespondsProblem::find()->with('interview')
            ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
            ->andWhere(['confirm_id' => $model->getId()])->andWhere(['not', ['interview_confirm_problem.id' => null]])->count();

        $count_positive = (int)RespondsProblem::find()->with('interview')
            ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
            ->andWhere(['confirm_id' => $model->getId(), 'interview_confirm_problem.status' => '1'])->count();


        if (Yii::$app->request->isAjax) {

            if (($model->gcps  && $model->getCountPositive() <= $count_positive && $model->problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) || (count($model->responds) === $count_descInterview && $model->getCountPositive() <= $count_positive && $model->problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED)) {

                $response =  [
                    'success' => true,
                    'renderAjax' => $this->renderAjax('/gcps/create', [
                        'confirmProblem' => $model,
                        'model' => $formCreateGcp,
                        'task' => $task,
                        'segment' => $model->problem->segment,
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
            /** @var $model ConfirmProblem */
            $model = ConfirmProblem::find(false)
                ->andWhere(['id' => $task->getHypothesisId()])
                ->one();

            /** @var $problem Problems */
            $problem = Problems::find(false)
                ->andWhere(['id' => $model->getProblemId()])
                ->one();

            /** @var $segment ConfirmSegment */
            $confirmSegment = ConfirmSegment::find(false)
                ->andWhere(['segment_id' => $problem->getSegmentId()])
                ->one();

            /** @var $segment Segments */
            $segment = Segments::find(false)
                ->andWhere(['id' => $problem->getSegmentId()])
                ->one();

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $problem->getProjectId()])
                ->one();

            /** @var $questions QuestionsConfirmProblem[] */
            $questions = QuestionsConfirmProblem::find(false)
                ->andWhere(['confirm_id' => $model->getId()])
                ->all();

            $countContractorResponds = (int)RespondsProblem::find()
                ->andWhere(['not', ['contractor_id' => null]])
                ->andWhere(['confirm_id' => $model->getId()])
                ->count();

            $response = [
                'renderAjax' => $this->renderAjax('data-confirm-hypothesis', [
                    'isTaskMarketing' => $task->activity->getTitle() === 'Маркетинг',
                    'model' => $model,
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
     * @return ConfirmProblem|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): ?ConfirmProblem
    {
        if (!$isOnlyNotDelete) {
            $model = ConfirmProblem::find(false)
                ->andWhere(['id' => $id])
                ->one();
        } else {
            $model = ConfirmProblem::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}