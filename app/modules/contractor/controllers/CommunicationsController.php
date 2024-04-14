<?php

namespace app\modules\contractor\controllers;

use app\models\ContractorCommunicationResponse;
use app\models\ContractorCommunications;
use app\models\ContractorCommunicationTypes;
use app\models\ContractorProjectAccess;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\User;
use app\modules\contractor\models\form\FormCreateCommunicationResponse;
use app\services\MailerService;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CommunicationsController extends AppContractorController
{
    public $layout = '@app/modules/contractor/views/layouts/main';

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'notifications') {

            $user = User::findOne((int)Yii::$app->request->get('id'));
            if (!$user) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $currentUser->getId() === $user->getId()) {

                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }

    }


    /**
     * Страница "Уведомления (коммуникации)"
     * для исполнителя
     *
     * @param int $id
     * @return string
     */
    public function actionNotifications(int $id): string
    {
        $user = User::findOne($id);
        // Проекты, по которым у исполнителя есть коммуникации
        $projects = Projects::find(false)
            ->leftJoin('contractor_communications', '`contractor_communications`.`project_id` = `projects`.`id`')
            ->andWhere(['or', ['contractor_communications.sender_id' => $id], ['contractor_communications.adressee_id' => $id]])
            ->orderBy('contractor_communications.id DESC')
            ->all();

        return $this->render('notifications', [
            'projects' => $projects,
            'user' => $user,
        ]);
    }


    /**
     * Получить уведомления
     * (коммуникации) по проекту
     *
     * @param int $project_id
     * @return array|bool
     */
    public function actionGetCommunications(int $project_id)
    {
        if(Yii::$app->request->isAjax) {

            $response = $this->responseForGetCommunications($project_id);
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $project_id
     * @return array
     */
    private function responseForGetCommunications(int $project_id): array
    {
        $communications = ContractorCommunications::find()
            ->andWhere(['project_id' => $project_id])
            ->andWhere(['not', ['type' => ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT]])
            ->andWhere(['not', ['type' => ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK]])
            ->andWhere(['or', ['adressee_id' => Yii::$app->user->getId()], ['sender_id' => Yii::$app->user->getId()]])
            ->orderBy('id DESC')
            ->all();

        return [
            'renderAjax' => $this->renderAjax('ajax_get_communications', [
                'communications' => $communications])
        ];
    }


    /**
     * Прочтение уведомления
     * (коммуникации) по проекту
     * исполнителем
     *
     * @param int $id
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionReadCommunication(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $response = $this->responseForReadCommunication($id);
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
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
        $countUnreadCommunications = $user->getCountUnreadCommunications();
        $countUnreadCommunicationsByProject = $user->getCountUnreadCommunicationsByProject($communication->getProjectId());

        return [
            'project_id' => $communication->getProjectId(),
            'countUnreadCommunications' => $countUnreadCommunications,
            'countUnreadCommunicationsByProject' => $countUnreadCommunicationsByProject
        ];
    }


    /**
     * Получить форму для ответа
     * на уведомление (коммуникацию)
     *
     * @param int $id
     * @return bool|array
     */
    public function actionGetFormCommunicationResponse(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = new FormCreateCommunicationResponse();
            $communication = ContractorCommunications::findOne($id);

            $response = ['renderAjax' => $this->renderAjax('ajax_get_form_communication_response', [
                'model' => $model, 'communication' => $communication])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Метод для отправки
     * коммуникации
     *
     * @param int $addressee_id
     * @param int $project_id
     * @param int $type
     * @param int $activity_id
     * @param int $triggered_communication_id
     * @param int|null $stage
     * @param int|null $stage_id
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionSend(int $addressee_id, int $project_id, int $type, int $activity_id, int $triggered_communication_id, int $stage = null, int $stage_id = null)
    {
        if(Yii::$app->request->isAjax) {

            // Создаем новую коммуникацию
            $communication = new ContractorCommunications();
            $communication->setParams($addressee_id, $project_id, $activity_id, $type, $stage, $stage_id, $triggered_communication_id);
            if ($communication->save()) {
                // Создаем объект содержащий ответ по созданной коммуникации
                $communicationResponse = new FormCreateCommunicationResponse();
                $communicationResponse->setCommunicationId($communication->getId());
                if ($communicationResponse->load(Yii::$app->request->post())) {
                    if ($communicationResponse->create()) {
                        // Получаем коммуникацию, в ответ на которую была создана данная коммуникация
                        $communicationAnswered = $communication->communicationAnswered;

                        // Если ответ исполнителя отрицательный, то у коммуникации на которую была создана данная коммуникация,
                        // то аннулируем доступ к проекту
                        if ($communication->communicationResponse->getAnswer() === ContractorCommunicationResponse::NEGATIVE_RESPONSE) {

                            $communicationAnsweredAccessToProject = $communicationAnswered->contractorProjectAccess;
                            $communicationAnsweredAccessToProject->setDateStop(time());
                            $communicationAnsweredAccessToProject->update();

                            $accessToProject = new ContractorProjectAccess();
                            $accessToProject->setParams($addressee_id, $project_id, $communication);
                            $accessToProject->save();
                        }

                        // Делаем коммуникацию прочитанной (отвеченной)
                        $result_ReadCommunication = $this->responseForReadCommunication($communicationAnswered->getId());

                        // Отправка письма проектанту на почту
                        $this->sendCommunicationToEmail($communication);

                        // Получить обновленные коммуникации
                        $result_GetCommunications =  $this->actionGetCommunications($project_id);
                        /** @var array $response */
                        $response = array_merge($result_ReadCommunication, $result_GetCommunications);
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }
            }
        }
        return false;
    }


    /**
     * Отправка письма с уведомлением
     * проектанту
     *
     * @param ContractorCommunications $communication
     * @return bool
     */
    public function sendCommunicationToEmail(ContractorCommunications $communication): bool
    {
        $contractor = User::findOne($communication->getSenderId());
        $user = User::findOne($communication->getAdresseeId());

        if ($contractor) {
            return MailerService::send(
                $user->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__FromContractorToSimpleUser',
                ['contractor' => $contractor, 'communication' => $communication]
            );
        }

        return false;
    }
}
