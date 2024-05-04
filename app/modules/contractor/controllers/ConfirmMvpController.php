<?php

namespace app\modules\contractor\controllers;

use app\models\ClientSettings;
use app\models\ConfirmMvp;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\SearchForm;
use app\models\Gcps;
use app\models\Mvps;
use app\models\PatternHttpException;
use app\models\Problems;
use app\models\Projects;
use app\models\QuestionsConfirmMvp;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\User;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ConfirmMvpController extends AppContractorController
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
            if (!$task || $task->getType() !== StageExpertise::CONFIRM_MVP) {
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
            if (!$task || $task->getType() !== StageExpertise::CONFIRM_MVP) {
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

        $mvp = Mvps::findOne($model->getMvpId());
        $gcp = Gcps::findOne($mvp->getGcpId());
        $problem = Problems::findOne($mvp->getProblemId());
        $segment = Segments::findOne($mvp->getSegmentId());
        $project = Projects::findOne($mvp->getProjectId());
        $questions = QuestionsConfirmMvp::findAll(['confirm_id' => $task->getHypothesisId()]);

        return $this->render('task', [
            'task' => $task,
            'model' => $model,
            'mvp' => $mvp,
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

        /** @var Mvps $mvp */
        $mvp = Mvps::find(false)
            ->andWhere(['id' => $model->getMvpId()])
            ->one();

        /** @var Gcps $gcp */
        $gcp = Gcps::find(false)
            ->andWhere(['id' => $mvp->getGcpId()])
            ->one();

        /** @var Problems $problem */
        $problem = Problems::find(false)
            ->andWhere(['id' => $mvp->getProblemId()])
            ->one();

        /** @var Segments $segment */
        $segment = Segments::find(false)
            ->andWhere(['id' => $mvp->getSegmentId()])
            ->one();

        /** @var Projects $project */
        $project = Projects::find(false)
            ->andWhere(['id' => $mvp->getProjectId()])
            ->one();

        /** @var QuestionsConfirmMvp[] $questions */
        $questions = QuestionsConfirmMvp::find(false)
            ->andWhere(['confirm_id' => $task->getHypothesisId()])
            ->all();

        return $this->render('task', [
            'task' => $task,
            'model' => $model,
            'mvp' => $mvp,
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
     * @return array|false
     */
    public function actionShowDataConfirmHypothesis(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($id);
            /** @var $model ConfirmMvp */
            $model = ConfirmMvp::find(false)
                ->andWhere(['id' => $task->getHypothesisId()])
                ->one();

            /** @var $mvp Mvps */
            $mvp = Mvps::find(false)
                ->andWhere(['id' => $model->getMvpId()])
                ->one();

            /** @var $gcp Gcps */
            $gcp = Gcps::find(false)
                ->andWhere(['id' => $mvp->getGcpId()])
                ->one();

            /** @var $problem Problems */
            $problem = Problems::find(false)
                ->andWhere(['id' => $mvp->getProblemId()])
                ->one();

            /** @var $segment ConfirmSegment */
            $confirmSegment = ConfirmSegment::find(false)
                ->andWhere(['segment_id' => $mvp->getSegmentId()])
                ->one();

            /** @var $segment Segments */
            $segment = Segments::find(false)
                ->andWhere(['id' => $mvp->getSegmentId()])
                ->one();

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $mvp->getProjectId()])
                ->one();

            /** @var $questions QuestionsConfirmMvp[] */
            $questions = QuestionsConfirmMvp::find(false)
                ->andWhere(['confirm_id' => $model->getId()])
                ->all();

            $response = [
                'renderAjax' => $this->renderAjax('data-confirm-hypothesis', [
                    'model' => $model,
                    'mvp' => $mvp,
                    'gcp' => $gcp,
                    'problem' => $problem,
                    'confirmSegment' => $confirmSegment,
                    'segment' => $segment,
                    'project' => $project,
                    'questions' => $questions,
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
     * @return ConfirmMvp|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): ?ConfirmMvp
    {
        if (!$isOnlyNotDelete) {
            $model = ConfirmMvp::find(false)
                ->andWhere(['id' => $id])
                ->one();
        } else {
            $model = ConfirmMvp::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
