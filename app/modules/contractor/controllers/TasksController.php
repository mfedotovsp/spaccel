<?php

namespace app\modules\contractor\controllers;

use app\models\ContractorTaskFiles;
use app\models\ContractorTasks;
use app\models\PatternHttpException;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use Yii;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TasksController extends AppContractorController
{
    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'complete') {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('id'));
            if (!$task) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }else{
            return parent::beforeAction($action);
        }
    }

    /**
     * @param int $id
     * @return bool[]|false
     * @throws \Throwable
     */
    public function actionComplete(int $id)
    {
        if (Yii::$app->request->isAjax) {
            $response = ['success' => false];
            $formTaskComplete = new FormTaskComplete();
            $task = ContractorTasks::findOne($id);
            if ($formTaskComplete->load(Yii::$app->request->post()) && $task->changeStatus(ContractorTasks::TASK_STATUS_COMPLETED, $formTaskComplete->getComment())) {
                $formTaskComplete->setTaskId($task->getId());
                $formTaskComplete->uploadPresentFiles();
                $response = ['success' => true];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }

    /**
     * @param int $id
     * @return \yii\console\Response|Response
     * @throws NotFoundHttpException
     */
    public function actionDownload(int $id)
    {
        $model = ContractorTaskFiles::findOne($id);
        $task = $model->task;
        $user = $task->contractor;
        $path = UPLOAD.'/user-'.$user->getId().'/tasks/task-'.$task->getId().'/files/';
        $file = $path . $model->getServerFile();

        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file, $model->getFileName());
        }
        throw new NotFoundHttpException('Данный файл не найден');
    }

    /**
     * @param int $id
     * @return array|Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDeleteFile(int $id)
    {
        $model = ContractorTaskFiles::findOne($id);
        $task = $model->task;
        $user = $task->contractor;
        $path = UPLOAD.'/user-'.$user->getId().'/tasks/task-'.$task->getId().'/files/';

        if(unlink($path . $model->getServerFile()) && $model->delete()) {
            $models = ContractorTaskFiles::findAll(['task_id' => $task->getId()]);

            if (Yii::$app->request->isAjax)
            {
                $response =  [
                    'success' => true,
                    'count_files' => count($models),
                    'task_id' => $task->getId(),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            return $this->refresh();
        }
        throw new NotFoundHttpException('Данный файл не найден');
    }
}