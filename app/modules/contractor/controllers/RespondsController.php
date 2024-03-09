<?php

namespace app\modules\contractor\controllers;

use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\UpdateRespondGcpForm;
use app\models\forms\UpdateRespondMvpForm;
use app\models\forms\UpdateRespondProblemForm;
use app\models\forms\UpdateRespondSegmentForm;
use app\models\PatternHttpException;
use app\models\RespondsGcp;
use app\models\RespondsMvp;
use app\models\RespondsProblem;
use app\models\RespondsSegment;
use app\models\StageConfirm;
use app\models\User;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер с методами редактирования и получения информации
 * о респондентах, которые проходят интервью при подтверждении гипотезы
 *
 * Class RespondsController
 * @package app\modules\contractor\controllers
 */
class RespondsController extends AppContractorController
{
    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'create') {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('id'));
            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }else{
            return parent::beforeAction($action);
        }
    }


    /**
     * @param int $stage
     * @param int $id
     * @param int $taskId
     * @return array|bool
     * @throws \Exception
     */
    public function actionGetDataCreateForm(int $stage, int $id, int $taskId)
    {
        if(Yii::$app->request->isAjax) {
            try {
                $confirm = self::getConfirm($stage, $id);
                $classRespond = self::getClassModel($stage);

                /** @var RespondsSegment[] | RespondsProblem[] | RespondsGcp[] | RespondsMvp[] $responds */
                $responds = $classRespond::find()
                    ->andWhere(['confirm_id' => $confirm->getId()])
                    ->andWhere(['info_respond' => ''])
                    ->andWhere(['place_interview' => ''])
                    ->andWhere(['date_plan' => null])
                    ->andWhere(['contractor_id' => null])
                    ->andWhere(['task_id' => null])
                    ->all();

                $firstFreeRespond = null;
                foreach ($responds as $respond) {
                    if (preg_match('/^Респондент \d+$/', $respond->getName())) {
                        $firstFreeRespond = $respond;
                        break;
                    }
                }

                if (!$firstFreeRespond) {
                    $response = ['renderAjax' => $this->renderAjax('notExistFreeRespond')];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

                $model = self::getUpdateModel($stage, $firstFreeRespond->getId());
                $response = ['renderAjax' => $this->renderAjax('form', [
                    'confirm' => $confirm,
                    'model' => $model,
                    'isOnlyNotDelete' => true,
                    'typeForm' => 'create',
                    'taskId' => $taskId,
                    'disabled' => false
                ])];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage(), 500);
            }
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return array|bool
     */
    public function actionGetDataUpdateForm(int $stage, int $id, bool $isOnlyNotDelete = true)
    {
        $model = self::getUpdateModel($stage, $id, $isOnlyNotDelete);
        $task = ContractorTasks::findOne($model->getTaskId());
        $confirm = $model->findConfirm($isOnlyNotDelete);
        $disabled = true;
        if ($task && in_array($task->getStatus(), [
                ContractorTasks::TASK_STATUS_NEW,
                ContractorTasks::TASK_STATUS_PROCESS,
                ContractorTasks::TASK_STATUS_RETURNED
            ], true)) {
            $disabled = false;
        }

        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('form', [
                'confirm' => $confirm,
                'model' => $model,
                'isOnlyNotDelete' => $isOnlyNotDelete,
                'typeForm' => 'update',
                'taskId' => $task ? $task->getId() : null,
                'disabled' => $disabled
            ])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @param int $taskId
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionUpdate(int $stage, int $id, int $taskId)
    {
        $task = ContractorTasks::findOne($taskId);
        $model = self::getUpdateModel($stage, $id);
        $model->setTaskId($task->getId());
        $model->setContractorId($task->getContractorId());

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            if ($model->validate(['name'])){

                if ($model->update($task)){

                    if ($task->getStatus() === ContractorTasks::TASK_STATUS_NEW) {
                        $task->changeStatus(ContractorTasks::TASK_STATUS_PROCESS);
                    }

                    $response = ['taskId' => $task->getId()];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

            }else{
                $response = ['error' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return ConfirmGcp|ConfirmMvp|ConfirmProblem|ConfirmSegment|bool|null
     */
    private static function getConfirm(int $stage, int $id, bool $isOnlyNotDelete = true)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            /** @var ConfirmSegment $confirm */
            $confirm = $isOnlyNotDelete ?
                ConfirmSegment::findOne($id) :
                ConfirmSegment::find(false)
                    ->andWhere(['id' => $id])
                    ->one();

            return $confirm;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            /** @var ConfirmProblem $confirm */
            $confirm = $isOnlyNotDelete ?
                ConfirmProblem::findOne($id) :
                ConfirmProblem::find(false)
                    ->andWhere(['id' => $id])
                    ->one();

            return $confirm;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            /** @var ConfirmGcp $confirm */
            $confirm = $isOnlyNotDelete ?
                ConfirmGcp::findOne($id) :
                ConfirmGcp::find(false)
                    ->andWhere(['id' => $id])
                    ->one();

            return $confirm;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            /** @var ConfirmMvp $confirm */
            $confirm = $isOnlyNotDelete ?
                ConfirmMvp::findOne($id) :
                ConfirmMvp::find(false)
                    ->andWhere(['id' => $id])
                    ->one();

            return $confirm;
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return UpdateRespondProblemForm|UpdateRespondGcpForm|UpdateRespondMvpForm|UpdateRespondSegmentForm|bool
     */
    private static function getUpdateModel(int $stage, int $id, bool $isOnlyNotDelete = true)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return new UpdateRespondSegmentForm($id, $isOnlyNotDelete);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new UpdateRespondProblemForm($id, $isOnlyNotDelete);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return new UpdateRespondGcpForm($id, $isOnlyNotDelete);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return new UpdateRespondMvpForm($id, $isOnlyNotDelete);
        }
        return false;
    }


    /**
     * @param int $stage
     * @return string
     * @throws \Exception
     */
    private static function getClassModel(int $stage): string
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return RespondsSegment::class;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return RespondsProblem::class;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return RespondsGcp::class;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return RespondsMvp::class;
        }
        throw new \Exception('Указан не существующий тип этапа проекта');
    }
}