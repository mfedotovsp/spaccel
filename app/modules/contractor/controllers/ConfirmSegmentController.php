<?php

namespace app\modules\contractor\controllers;

use app\models\ClientSettings;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\FormCreateProblem;
use app\models\forms\SearchForm;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\QuestionsConfirmSegment;
use app\models\RespondsSegment;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ConfirmSegmentController extends AppContractorController
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
            if (!$task || $task->getType() !== StageExpertise::CONFIRM_SEGMENT) {
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

        } elseif ($action->id === 'view-trash') {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('id'));
            if (!$task || $task->getType() !== StageExpertise::CONFIRM_SEGMENT) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserSimple($currentUser->getUsername()) && $task->project->getUserId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else {
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

        $segment = Segments::findOne($model->getSegmentId());
        $project = Projects::findOne($segment->getProjectId());
        $questions = QuestionsConfirmSegment::findAll(['confirm_id' => $task->getHypothesisId()]);

        if ($task->activity->getTitle() === 'Маркетинг') {
            return $this->render('task_marketing', [
                'task' => $task,
                'model' => $model,
                'segment' => $segment,
                'project' => $project,
                'questions' => $questions,
                'searchForm' => new SearchForm()
            ]);
        }

        return $this->render('task', [
            'task' => $task,
            'model' => $model,
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

        /** @var Segments $segment */
        $segment = Segments::find(false)
            ->andWhere(['id' => $model->getSegmentId()])
            ->one();

        /** @var Projects $project */
        $project = Projects::find(false)
            ->andWhere(['id' => $segment->getProjectId()])
            ->one();

        /** @var QuestionsConfirmSegment[] $questions */
        $questions = QuestionsConfirmSegment::find(false)
            ->andWhere(['confirm_id' => $task->getHypothesisId()])
            ->all();

        if ($task->activity->getTitle() === 'Маркетинг') {
            return $this->render('task_marketing', [
                'task' => $task,
                'model' => $model,
                'segment' => $segment,
                'project' => $project,
                'questions' => $questions,
                'searchForm' => new SearchForm()
            ]);
        }

        return $this->render('task', [
            'task' => $task,
            'model' => $model,
            'segment' => $segment,
            'project' => $project,
            'questions' => $questions,
            'searchForm' => new SearchForm()
        ]);
    }


    /**
     * Проверка данных подтверждения на этапе генерации ГПС
     *
     * @param int $id
     * @return array|bool
     */
    public function actionDataAvailabilityForNextStep(int $id)
    {
        $task = ContractorTasks::findOne($id);
        $model = ConfirmSegment::findOne($task->getHypothesisId());
        $formCreateProblem = new FormCreateProblem($model->hypothesis);

        $count_descInterview = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $model->getId()])->andWhere(['not', ['interview_confirm_segment.id' => null]])->count();

        $count_positive = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $model->getId(), 'interview_confirm_segment.status' => '1'])->count();

        if (Yii::$app->request->isAjax) {

            if (($model->problems  && $model->getCountPositive() <= $count_positive && $model->hypothesis->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) || (count($model->responds) === $count_descInterview && $model->getCountPositive() <= $count_positive && $model->hypothesis->getExistConfirm() === StatusConfirmHypothesis::COMPLETED)) {

                $response =  [
                    'success' => true,
                    'cacheExpectedResultsInterview' => $formCreateProblem->_cacheManager->getCache($formCreateProblem->cachePath, 'formCreateHypothesisCache')['FormCreateProblem']['_expectedResultsInterview'],
                    'renderAjax' => $this->renderAjax('/problems/create', [
                        'confirmSegment' => $model,
                        'model' => $formCreateProblem,
                        'task' => $task,
                        'responds' => RespondsSegment::find()->with('interview')
                            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
                            ->andWhere(['confirm_id' => $model->getId(), 'interview_confirm_segment.status' => '1'])->all(),
                    ]),
                ];

            }else{
                $response = ['error' => true];
            }
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
            /** @var $model ConfirmSegment */
            $model = ConfirmSegment::find(false)
                ->andWhere(['id' => $task->getHypothesisId()])
                ->one();

            /** @var $segment Segments */
            $segment = Segments::find(false)
                ->andWhere(['id' => $model->getSegmentId()])
                ->one();

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $segment->getProjectId()])
                ->one();

            /** @var $questions QuestionsConfirmSegment[] */
            $questions = QuestionsConfirmSegment::find(false)
                ->andWhere(['confirm_id' => $model->getId()])
                ->all();

            $countContractorResponds = (int)RespondsSegment::find()
                ->andWhere(['not', ['contractor_id' => null]])
                ->andWhere(['confirm_id' => $model->getId()])
                ->count();

            $response = [
                'renderAjax' => $this->renderAjax('data-confirm-hypothesis', [
                    'isTaskMarketing' => $task->activity->getTitle() === 'Маркетинг',
                    'model' => $model,
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
     * @return ConfirmSegment|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): ?ConfirmSegment
    {
        if (!$isOnlyNotDelete) {
            $model = ConfirmSegment::find(false)
                ->andWhere(['id' => $id])
                ->one();
        } else {
            $model = ConfirmSegment::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
