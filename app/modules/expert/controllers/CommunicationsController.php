<?php


namespace app\modules\expert\controllers;


use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\PatternHttpException;
use app\models\ProjectCommunications;
use app\models\Projects;
use app\models\User;
use app\modules\expert\models\form\FormCreateCommunicationResponse;
use app\services\MailerService;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class CommunicationsController
 * @package app\modules\expert\controllers
 */
class CommunicationsController extends AppExpertController
{

    public $layout = '@app/modules/expert/views/layouts/main';


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

            if (User::isUserExpert($currentUser->getUsername()) && $currentUser->getId() === $user->getId()) {

                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }

    }


    /**
     * Уведомления (коммуникации)
     * эксперта по проектам
     *
     * @param int $id
     * @return string
     */
    public function actionNotifications(int $id): string
    {
        $user = User::findOne($id);
        // Проекты, по которым у эксперта есть коммуникации
        $projects = Projects::find(false)
            ->distinct()
            ->leftJoin('project_communications', '`project_communications`.`project_id` = `projects`.`id`')
            ->andWhere(['or', ['project_communications.sender_id' => $id], ['project_communications.adressee_id' => $id]])
            ->orderBy('project_communications.id DESC')
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
        $communications = ProjectCommunications::find()
            ->andWhere(['project_id' => $project_id])
            ->andWhere(['not', ['type' => CommunicationTypes::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE]])
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
     * экспертом
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
        $communication = ProjectCommunications::findOne($id);
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
            $communication = ProjectCommunications::findOne($id);

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
     * @param int $adressee_id
     * @param int $project_id
     * @param int $type
     * @param int $triggered_communication_id
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionSend(int $adressee_id, int $project_id, int $type, int $triggered_communication_id)
    {
        if(Yii::$app->request->isAjax) {

            // Создаем новую коммуникацию
            $communication = new ProjectCommunications();
            $communication->setParams($adressee_id, $project_id, $type);
            $communication->setTriggeredCommunicationId($triggered_communication_id);
            if ($communication->save()) {
                // Создаем объект содержащий ответ по созданной коммуникации
                $communicationResponse = new FormCreateCommunicationResponse();
                $communicationResponse->setCommunicationId($communication->getId());
                if ($communicationResponse->load(Yii::$app->request->post())) {
                    if ($communicationResponse->create()) {
                        // Получаем коммуникацию, в ответ на которую была создана данная коммуникация
                        $communicationAnswered = $communication->communicationAnswered;

                        // Если ответ эксперта отрицательный, то у коммуникации на которую была создана данная коммуникация
                        // меняем у объекта доступа к проекту параметр cancel,
                        // т.е. аннулируем доступ к проекту
                        if ($communication->communicationResponse->getAnswer() === CommunicationResponse::NEGATIVE_RESPONSE) {
                            $communicationAnsweredAccessToProject = $communicationAnswered->userAccessToProject;
                            $communicationAnsweredAccessToProject->setCancel();
                            $communicationAnsweredAccessToProject->update();
                        }

                        // Делаем коммуникацию прочитанной (отвеченной)
                        $result_ReadCommunication = $this->responseForReadCommunication($communicationAnswered->getId());

                        // Отправка письма админу организации на почту
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
     * на главного админа
     *
     * @param ProjectCommunications $communication
     * @return bool
     */
    public function sendCommunicationToEmail(ProjectCommunications $communication): bool
    {
        $user = User::findOne($communication->getSenderId());
        $admin = User::findOne($communication->getAdresseeId());

        if ($user) {
            return MailerService::send(
                $admin->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__FromExpertToMainAdmin',
                ['user' => $user, 'communication' => $communication]
            );
        }

        return false;
    }




}
