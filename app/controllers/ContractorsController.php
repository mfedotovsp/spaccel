<?php

namespace app\controllers;

use app\models\ContractorActivities;
use app\models\ContractorCommunications;
use app\models\ContractorCommunicationTypes;
use app\models\ContractorProject;
use app\models\ContractorProjectAccess;
use app\models\ContractorUsers;
use app\models\forms\SearchContractorsForm;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\User;
use app\services\MailerService;
use Exception;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Контроллер с методами создания,
 * редактирования и получения информации
 * по исполнителям проектов
 *
 * Class ContractorsController
 * @package app\controllers
 */
class ContractorsController extends AppUserPartController
{

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'index') {
            $model = User::findOne((int)Yii::$app->request->get('id'));
            if (!$model) {
                PatternHttpException::noData();
            }

            if (($model->getId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'add'){
            if (User::isUserSimple($currentUser->getUsername())) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif (in_array($action->id, ['communication-projects', 'tasks-projects'], false)) {
            $model = User::findOne((int)Yii::$app->request->get('id'));
            if (!$model) {
                PatternHttpException::noData();
            }

            if (ContractorUsers::findOne(['contractor_id' => $model->getId(), 'user_id' => $currentUser->getId()])) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else {
            return parent::beforeAction($action);
        }
    }


    /**
     * @param ContractorCommunications $communication1
     * @param ContractorCommunications $communication2
     * @return int
     */
    private function sortCommunications(ContractorCommunications $communication1, ContractorCommunications $communication2): int
    {
        return $communication1->getCreatedAt() > $communication2->getCreatedAt() ? -1 : 1;
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionIndex(int $id): string
    {
        $lastCommunications = [];
        $user = User::findOne($id);
        $contractorUsers = $user->contractorUsers;
        foreach ($contractorUsers as $contractorUser) {
            $lastCommunications[] = ContractorCommunications::getLastCommunicationWithContractor($contractorUser->contractor->getId());
        }

        usort($lastCommunications, static function (ContractorCommunications $communication, ContractorCommunications $communicationNext) {
            return $communication->getCreatedAt() > $communicationNext->getCreatedAt() ? -1 : 1;
        });

        $models = [];
        foreach ($lastCommunications as $communication) {
            /** @var ContractorCommunications $communication */
            $models[] = $communication->contractor;
        }

        return $this->render('index', [
            'user' => $user,
            'models' => $models,
        ]);
    }


    /**
     * Получить исполнителей по данным поиска для назначения на проект
     *
     * @return array|false
     */
    public function actionGetList()
    {
        if(Yii::$app->request->isAjax) {
            $formSearch = new SearchContractorsForm();
            if ($formSearch->load(Yii::$app->request->post())) {
                $models = $formSearch->search();
                $response = [
                    'success' => true,
                    'renderAjax' => $this->renderAjax('list_ajax', ['models' => $models]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            $response = ['success' => false];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * Страница добавление исполнителя на проект (поиск исполнителей и отправка запроса)
     *
     * @return string
     */
    public function actionAdd(): string
    {
        $formSearch = new SearchContractorsForm();
        $projects = Projects::findAll(['user_id' => Yii::$app->user->getId()]);
        $projectOptions = ArrayHelper::map($projects, 'id', 'project_name');
        $activitiesContractor = ContractorActivities::find()->all();
        $activityOptions = ArrayHelper::map($activitiesContractor, 'id', 'title');

        return $this->render('add', [
            'formSearch' => $formSearch,
            'projectOptions' => $projectOptions,
            'activityOptions' => $activityOptions
        ]);
    }


    /**
     * Отправка письма с уведомлением
     * на почту исполнителя
     *
     * @param ContractorCommunications $communication
     * @return bool
     */
    public function sendCommunicationToEmail(ContractorCommunications $communication): bool
    {
        /* @var $user User */
        $user = User::findOne($communication->getAdresseeId());

        if ($user) {
            return MailerService::send(
                $user->getEmail(),
                'Вам пришло новое уведомление на сайте '. Yii::$app->params['siteName'],
                'communications__FromSimpleUserToContractor',
                ['user' => $user, 'communication' => $communication]
            );
        }

        return false;
    }


    /**
     * @param int $id
     * @return array
     * @throws StaleObjectException
     * @throws Throwable
     */
    private function responseForReadCommunication(int $id): array
    {
        $communication = ContractorCommunications::findOne($id);
        $communication->setStatusRead();
        $communication->update();

        $user = User::findOne($communication->getAdresseeId());
        $countUnreadCommunications = $user->countUnreadCommunicationsFromContractors;
        $countUnreadCommunicationsByProject = $user->getCountUnreadCommunicationsByProject($communication->getProjectId(), $communication->getSenderId());

        return [
            'project_id' => $communication->getProjectId(),
            'countUnreadCommunications' => $countUnreadCommunications,
            'countUnreadCommunicationsByProject' => $countUnreadCommunicationsByProject
        ];
    }


    /**
     * Отправка коммуникации исполнителю
     *
     * @param int $adressee_id
     * @param int $type
     * @param int $project_id
     * @param int $activity_id
     * @param int|null $stage
     * @param int|null $stage_id
     * @param int|null $triggered_communication_id
     * @return array|false
     * @throws Throwable
     */
    public function actionSendCommunication(
        int $adressee_id,
        int $type,
        int $project_id,
        int $activity_id,
        int $stage = null,
        int $stage_id = null,
        int $triggered_communication_id = null)
    {
        if (Yii::$app->request->isAjax) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $communication = new ContractorCommunications();
                $communication->setParams($adressee_id, $project_id, $activity_id, $type, $stage, $stage_id, $triggered_communication_id);

                if ($communication->save()) {
                    $accessToProject = new ContractorProjectAccess();
                    $accessToProject->setParams($adressee_id, $project_id, $communication);
                    if ($accessToProject->save() && ContractorUsers::getInstance($adressee_id, Yii::$app->user->getId())) {

                        if ($type === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT) {
                            User::createConversationContractor($communication->user, $communication->contractor);
                        }

                        if ($type === ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT) {
                            $contractorProject = new ContractorProject();
                            $contractorProject->setContractorId($adressee_id);
                            $contractorProject->setProjectId($project_id);
                            $contractorProject->setActivityId($activity_id);
                            $contractorProject->create();
                        }

                        if ($type === ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT) {
                            ContractorProject::remove($adressee_id, $project_id, $activity_id);
                        }

                        $result_ReadCommunication = [];
                        if (in_array($type, [
                            ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT,
                            ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT]
                        , true)) {
                            $result_ReadCommunication = $this->responseForReadCommunication($triggered_communication_id);
                        }

                        // Отправка письма на почту
                        $this->sendCommunicationToEmail($communication);

                        $result_SendCommunication = ['success' => true, 'type' => $type, 'project_id' => $project_id];
                        $response = array_merge($result_ReadCommunication, $result_SendCommunication);
                        $transaction->commit();
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                $transaction->rollBack();
                $response = ['success' => false];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            } catch (Exception $exception) {
                $transaction->rollBack();
                $response = ['success' => false];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|false
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionReadCommunication(int $id)
    {
        if (Yii::$app->request->isAjax) {
            $response = $this->responseForReadCommunication($id);
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * Страница для показа коммуникаций по проектам руководителя,
     * по которым он отправлял коммуникации исполнителю
     *
     * @param int $id
     * @return string
     */
    public function actionCommunicationProjects(int $id): string
    {
        $contractor = User::findOne($id);
        $contractorProjectIds = ContractorCommunications::find()
            ->select('project_id')
            ->distinct('project_id')
            ->andWhere(['or', ['adressee_id' => $contractor->getId()], ['sender_id' => $contractor->getId()]])
            ->andWhere(['or', ['adressee_id' => Yii::$app->user->getId()], ['sender_id' => Yii::$app->user->getId()]])
            ->asArray()
            ->all();

        $projectIds = [];
        foreach ($contractorProjectIds as $id) {
            $projectIds[] = $id['project_id'];
        }

        /** @var $projects Projects[] */
        $projects = Projects::find(false)
            ->andWhere(['in', 'id', $projectIds])
            ->andWhere(['user_id' => Yii::$app->user->getId()])
            ->all();

        return $this->render('communication-projects', [
            'projects' => $projects,
            'contractor' => $contractor
        ]);
    }


    /**
     * Получить коммуникации с исполнителем по проекту
     *
     * @param int $contractorId
     * @param int $projectId
     * @return array|false
     */
    public function actionGetCommunicationByProject(int $contractorId, int $projectId)
    {
        if (Yii::$app->request->isAjax) {

            $models = ContractorCommunications::find()
                ->andWhere(['or', ['adressee_id' => $contractorId], ['sender_id' => $contractorId]])
                ->andWhere(['or', ['adressee_id' => Yii::$app->user->getId()], ['sender_id' => Yii::$app->user->getId()]])
                ->andWhere(['in', 'type', ContractorCommunicationTypes::getListTypes()])
                ->andWhere(['project_id' => $projectId])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

            $response = [
                'success' => true,
                'renderAjax' => $this->renderAjax('communication_by_project_ajax', ['models' => $models]),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * Страница для показа заданий по проектам руководителя,
     * по которым был назначен исполнитель
     *
     * @param int $id
     * @return string
     */
    public function actionTaskProjects(int $id): string
    {
        $contractor = User::findOne($id);
        $contractorProjectIds = ContractorProject::find()
            ->select('project_id')
            ->distinct('project_id')
            ->andWhere([
                'contractor_id' => $contractor->getId(),
                'deleted_at' => null
            ])
            ->asArray()
            ->all();

        $projectIds = [];
        foreach ($contractorProjectIds as $id) {
            $projectIds[] = $id['project_id'];
        }

        /** @var $projects Projects[] */
        $projects = Projects::find(false)
            ->andWhere(['in', 'id', $projectIds])
            ->andWhere(['user_id' => Yii::$app->user->getId()])
            ->all();

        return $this->render('task-projects', [
            'projects' => $projects,
            'contractor' => $contractor
        ]);
    }
}
