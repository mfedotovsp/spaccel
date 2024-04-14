<?php


namespace app\modules\client\controllers;


use app\models\CommunicationPatterns;
use app\models\CommunicationTypes;
use app\models\DuplicateCommunications;
use app\models\PatternHttpException;
use app\models\ProjectCommunications;
use app\models\TypesAccessToExpertise;
use app\models\TypesDuplicateCommunication;
use app\models\User;
use app\models\UserAccessToProjects;
use app\modules\admin\models\form\FormExpertTypes;
use app\modules\admin\models\form\FormUpdateCommunicationPattern;
use app\services\MailerService;
use Throwable;
use Yii;
use yii\data\Pagination;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;


class CommunicationsController extends AppClientController
{

    /**
     * Количество уведомлений
     * на странице
     */
    public const NOTIFICATIONS_PAGE_SIZE = 20;


    public $layout = '@app/modules/client/views/layouts/users';


    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {

        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'settings') {

            if (User::isUserAdminCompany($currentUser->getUsername())) {
                return parent::beforeAction($action);
            }
            PatternHttpException::noAccess();

        }elseif ($action->id === 'notifications') {

            $user = User::findOne((int)Yii::$app->request->get('id'));
            if (!$user) {
                PatternHttpException::noData();
            }

            if (($currentUser->getId() === $user->getId()) && (User::isUserAdminCompany($currentUser->getUsername()) || User::isUserAdmin($currentUser->getUsername()))) {
                return parent::beforeAction($action);
            }
            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }

    }


    /**
     * Страница настройки
     * шаблонов коммуникаций
     *
     * @return string
     */
    public function actionSettings(): string
    {
        // Форма шаблона коммуникации
        $formPattern = new CommunicationPatterns();
        $initiator = Yii::$app->user->getId();
        // Список для выбора срока доступа к проекту
        $selection_project_access_period = array_combine(range(1,30), range(1,30));
        foreach ($selection_project_access_period as $k => $item) {
            if (in_array($item, [1, 21], true)) {
                $selection_project_access_period[$k] = $item . ' день';
            } elseif (in_array($item, [2, 3, 4, 22, 23, 24], true)) {
                $selection_project_access_period[$k] = $item . ' дня';
            } else {
                $selection_project_access_period[$k] = $item . ' дней';
            }
        }

        // Шаблоны коммуникации о готовности эксперта провести экспертизу
        $patternsCommunicationsAboutReadinessConductExpertise = CommunicationPatterns::find()
            ->andWhere(['communication_type' => CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE])
            ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
            ->orderBy('id DESC')
            ->all();

        // Шаблоны коммуникации отмена запроса о готовности эксперта провести экспертизу
        $patternsCommunicationsWithdrawsRequestAboutReadinessConductExpertise = CommunicationPatterns::find()
            ->andWhere(['communication_type' => CommunicationTypes::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE])
            ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
            ->orderBy('id DESC')
            ->all();

        // Шаблоны коммуникации назначение экперта на проект
        $patternsCommunicationsAppointsExpertProject = CommunicationPatterns::find()
            ->andWhere(['communication_type' => CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT])
            ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
            ->orderBy('id DESC')
            ->all();

        // Шаблоны коммуникации отказ эксперту в назначении на проект
        $patternsCommunicationsDoesNotAppointsExpertProject = CommunicationPatterns::find()
            ->andWhere(['communication_type' => CommunicationTypes::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT])
            ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
            ->orderBy('id DESC')
            ->all();

        // Шаблоны коммуникации отзыв эксперта с проекта
        $patternsCommunicationsWithdrawsExpertFromProject = CommunicationPatterns::find()
            ->andWhere(['communication_type' => CommunicationTypes::MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT])
            ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
            ->orderBy('id DESC')
            ->all();


        return $this->render('settings', [
            'formPattern' => $formPattern,
            'selection_project_access_period' => $selection_project_access_period,
            'patternsCARCE' => $patternsCommunicationsAboutReadinessConductExpertise,
            'patternsCWRARCE' => $patternsCommunicationsWithdrawsRequestAboutReadinessConductExpertise,
            'patternsCAEP' => $patternsCommunicationsAppointsExpertProject,
            'patternsCDNAEP' => $patternsCommunicationsDoesNotAppointsExpertProject,
            'patternsCWEFP' => $patternsCommunicationsWithdrawsExpertFromProject,
        ]);
    }


    /**
     * Создание нового шаблона коммуникации
     *
     * @param int $communicationType
     * @return array|bool
     */
    public function actionCreatePattern(int $communicationType)
    {
        if(Yii::$app->request->isAjax) {
            $formPattern = new CommunicationPatterns();
            if ($formPattern->load(Yii::$app->request->post())) {
                $formPattern->setParams($communicationType);
                $initiator = Yii::$app->user->getId();
                if ($formPattern->save()) {
                    if ($communicationType === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) {

                        $patternsCommunicationsAboutReadinessConductExpertise = CommunicationPatterns::find()
                            ->andWhere(['communication_type' => CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE])
                            ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
                            ->orderBy('id DESC')
                            ->all();

                        $response = ['renderAjax' => $this->renderAjax('ajax_patterns_carce', [
                            'patternsCARCE' => $patternsCommunicationsAboutReadinessConductExpertise])];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;

                    }

                    $patterns = CommunicationPatterns::find()
                        ->andWhere(['communication_type' => $communicationType])
                        ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
                        ->orderBy('id DESC')
                        ->all();

                    $response = ['renderAjax' => $this->renderAjax('ajax_patterns', [
                        'patterns' => $patterns, 'communicationType' => $communicationType])];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * Получить представление
     * одного шалона
     *
     * @param int $id
     * @return array|bool
     */
    public function getViewOnePattern(int $id)
    {
        if (Yii::$app->request->isAjax) {

            // Список для выбора срока доступа к проекту
            $selection_project_access_period = array_combine(range(1,30), range(1,30));
            foreach ($selection_project_access_period as $k => $item) {
                if (in_array($item, [1, 21], true)) {
                    $selection_project_access_period[$k] = $item . ' день';
                } elseif (in_array($item, [2, 3, 4, 22, 23, 24], true)) {
                    $selection_project_access_period[$k] = $item . ' дня';
                } else {
                    $selection_project_access_period[$k] = $item . ' дней';
                }
            }

            $pattern = CommunicationPatterns::findOne($id);

            $response = ['renderAjax' => $this->renderAjax('ajax_view_one_pattern', [
                'pattern' => $pattern, 'selection_project_access_period' => $selection_project_access_period])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Отмена редактирования
     * шаблона коммуникации
     *
     * @param int $id
     * @return array|bool
     */
    public function actionCancelEditPattern(int $id)
    {
        return $this->getViewOnePattern($id);
    }


    /**
     * Получить форму редактирования
     * шаблона коммуникации
     *
     * @param int $id
     * @param int $communicationType
     * @return array|bool
     */
    public function actionGetFormUpdateCommunicationPattern(int $id, int $communicationType)
    {
        if (Yii::$app->request->isAjax) {

            $formPattern = new FormUpdateCommunicationPattern($id, $communicationType);
            // Список для выбора срока доступа к проекту
            $selection_project_access_period = array_combine(range(1,30), range(1,30));
            foreach ($selection_project_access_period as $k => $item) {
                if (in_array($item, [1, 21], true)) {
                    $selection_project_access_period[$k] = $item . ' день';
                } elseif (in_array($item, [2, 3, 4, 22, 23, 24], true)) {
                    $selection_project_access_period[$k] = $item . ' дня';
                } else {
                    $selection_project_access_period[$k] = $item . ' дней';
                }
            }

            $response = ['renderAjax' => $this->renderAjax('ajax_form_update_pattern', [
                'formPattern' => $formPattern, 'selection_project_access_period' => $selection_project_access_period])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Редактирование
     * шаблона коммуникации
     * @param int $id
     * @param int $communicationType
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionUpdatePattern(int $id, int $communicationType)
    {
        if(Yii::$app->request->isAjax) {

            $formPattern = new FormUpdateCommunicationPattern($id, $communicationType);
            if ($formPattern->load(Yii::$app->request->post())) {
                $formPattern->update();
                return $this->getViewOnePattern($id);
            }
        }
        return false;
    }


    /**
     * Активация шаблона
     * коммуникации
     *
     * @param int $id
     * @param int $communicationType
     * @return bool|array
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionActivatePattern(int $id, int $communicationType)
    {
        if(Yii::$app->request->isAjax) {

            $initiator = Yii::$app->user->getId();
            /** @var CommunicationPatterns[] $patternsActive */
            $patternsActive = CommunicationPatterns::find()
                ->andWhere(['communication_type' => $communicationType, 'is_active' => CommunicationPatterns::ACTIVE])
                ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
                ->all();

            foreach ($patternsActive as $item) {
                $item->setIsActive(CommunicationPatterns::NO_ACTIVE);
                $item->update(true, ['is_active']);
            }

            $patternActivate = CommunicationPatterns::findOne($id);
            $patternActivate->setIsActive(CommunicationPatterns::ACTIVE);
            $patternActivate->update(true, ['is_active']);

            $patterns = CommunicationPatterns::find()
                ->andWhere(['communication_type' => $communicationType])
                ->andWhere(['initiator' => $initiator, 'is_remote' => CommunicationPatterns::NOT_REMOTE])
                ->orderBy('id DESC')
                ->all();

            if ($communicationType === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) {

                $response = ['renderAjax' => $this->renderAjax('ajax_patterns_carce', [
                    'patternsCARCE' => $patterns])];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            $response = ['renderAjax' => $this->renderAjax('ajax_patterns', [
                'patterns' => $patterns, 'communicationType' => $communicationType])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Деактивация шаблона
     * коммуникации
     *
     * @param int $id
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDeactivatePattern(int $id)
    {
        $pattern = CommunicationPatterns::findOne($id);

        if(Yii::$app->request->isAjax) {

            $pattern->setIsActive(CommunicationPatterns::NO_ACTIVE);
            $pattern->update(true, ['is_active']);
            return $this->getViewOnePattern($id);

        }
        return false;
    }


    /**
     * Удаление шаблона
     * коммуникации из списка
     *
     * @param int $id
     * @return bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDeletePattern(int $id): bool
    {
        $pattern = CommunicationPatterns::findOne($id);

        if(Yii::$app->request->isAjax) {

            $pattern->setIsRemote(CommunicationPatterns::REMOTE);
            $pattern->update(true, ['is_remote']);
            return true;
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
     * @param null|int $triggered_communication_id
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionSend(int $adressee_id, int $project_id, int $type, int $triggered_communication_id = null)
    {
        if(Yii::$app->request->isAjax) {

            // Данные из формы выбора типов деятельности эксперта при назначении на проект
            $postExpertTypes = $_POST['FormExpertTypes']['expert_types'] ?: null;

            $communication = new ProjectCommunications();
            $communication->setParams($adressee_id, $project_id, $type);
            $communication->setTriggeredCommunicationId($triggered_communication_id);
            if ($communication->save()) {
                $accessToProject = new UserAccessToProjects();
                $accessToProject->setParams($adressee_id, $project_id, $communication);
                if ($accessToProject->save()) {

                    $result_ReadCommunication = [];

                    if ($type === CommunicationTypes::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE) {

                        // Тип коммуникации "отмена запроса о готовности провести экспертизу"

                        // Устанавливаем параметр аннулирования предыдущей коммуникации
                        /** @var ProjectCommunications $communicationCanceled */
                        $communicationCanceled = ProjectCommunications::find()
                            ->andWhere([
                                'adressee_id' => $adressee_id,
                                'project_id' => $project_id,
                                'type' => CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE
                            ])
                            ->orderBy('id DESC')
                            ->one();

                        $communicationCanceled->setCancel();
                        $communicationCanceled->update();

                        $communicationCanceledUserAccessToProject = $communicationCanceled->userAccessToProject;
                        $communicationCanceledUserAccessToProject->setCancel();
                        $communicationCanceledUserAccessToProject->update();

                    } elseif ($type === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) {

                        // Тип коммуникации "назначение эксперта на проект"

                        // Типы доступных экспертиз по проекту
                        $typesAccessToExpertise = new TypesAccessToExpertise();
                        $typesAccessToExpertise->create($adressee_id, $project_id, $communication->getId(), $postExpertTypes);

                        // Прочтение коммуникации на которое поступил ответ
                        $result_ReadCommunication = $this->responseForReadCommunication($triggered_communication_id);

                        // Создание беседы между проектантом и экспертом
                        User::createConversationExpert($communication->project->user, $communication->expert);

                        // Отправка уведомления проектанту и трекеру
                        DuplicateCommunications::create($communication, $communication->project->user, TypesDuplicateCommunication::MAIN_ADMIN_TO_EXPERT);
                        DuplicateCommunications::create($communication, $communication->project->user->admin, TypesDuplicateCommunication::MAIN_ADMIN_TO_EXPERT);

                    } elseif ($type === CommunicationTypes::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT) {

                        // Тип коммуникации "отказ в проведении экспертизы"

                        // Прочтение коммуникации на которое поступил ответ
                        $result_ReadCommunication = $this->responseForReadCommunication($triggered_communication_id);

                    } elseif ($type === CommunicationTypes::MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT) {

                        // Тип коммуникации "отозвать эксперта с проекта"

                        // Отправка уведомления проектанту и трекеру
                        DuplicateCommunications::create($communication, $communication->project->user, TypesDuplicateCommunication::MAIN_ADMIN_TO_EXPERT);
                        DuplicateCommunications::create($communication, $communication->project->user->admin, TypesDuplicateCommunication::MAIN_ADMIN_TO_EXPERT);
                    }

                    // Отправка письма эксперту на почту
                    $this->sendCommunicationToEmail($communication);
                    $result_SendCommunication = ['success' => true, 'type' => $type, 'project_id' => $project_id];
                    $response = array_merge($result_ReadCommunication, $result_SendCommunication);
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * Получить форму выбора типов
     * эксперта при назначении на проект
     *
     * @param int $id
     * @return array|bool
     */
    public function actionGetFormTypesExpert(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('get_form_types_expert', [
                'communicationExpert' => ProjectCommunications::findOne($id),
                'formExpertTypes' => new FormExpertTypes()])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Получить коммуникации
     * по проекту
     *
     * @param int $id
     * @return bool|array
     */
    public function actionGetCommunications(int $id)
    {
        if(Yii::$app->request->isAjax) {

            // Допуски экспертов к проекту
            $admittedExperts = UserAccessToProjects::find()
                ->select(['user_id', 'project_id'])
                ->distinct('user_id')
                ->andWhere(['project_id' => $id])
                ->all();

            $response = ['renderAjax' => $this->renderAjax('ajax_get_communications', [
                'admittedExperts' => $admittedExperts, 'project_id' => $id]), 'project_id' => $id];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Отправка письма с уведомлением
     * на почту эксперта
     *
     * @param ProjectCommunications $communication
     * @return bool
     */
    public function sendCommunicationToEmail(ProjectCommunications $communication): bool
    {
        if ($user = User::findOne($communication->getAdresseeId())) {
            return MailerService::send(
                $user->getEmail(),
                'Вам пришло новое уведомление на сайте ' . Yii::$app->params['siteName'],
                'communications__FromMainAdminToExpert',
                ['user' => $user, 'communication' => $communication]
            );
        }

        return false;
    }


    /**
     * Страница
     * уведомлений
     *
     * @param int $id
     * @param int $page
     * @return string|Response
     */
    public function actionNotifications(int $id, int $page = 1)
    {
        $pageSize = self::NOTIFICATIONS_PAGE_SIZE;

        if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

            // Дублирующие коммуникации отправленные трекеру
            $query_communications = DuplicateCommunications::find()
                ->leftJoin('project_communications', '`project_communications`.`id` = `duplicate_communications`.`source_id`')
                ->andWhere(['duplicate_communications.adressee_id' => $id])
                ->orderBy('id DESC');

            $pages = new Pagination(['totalCount' => $query_communications->count(), 'page' => ($page - 1), 'pageSize' => $pageSize]);
            $pages->pageSizeParam = false; //убираем параметр $per-page
            $communications = $query_communications->offset($pages->offset)->limit($pageSize)->all();

            return $this->render('admin_notifications', [
                'communications' => $communications,
                'pages' => $pages,
            ]);

        }

        if (User::isUserAdminCompany(Yii::$app->user->identity['username'])) {

            $query_communications = ProjectCommunications::find()
                ->andWhere(['adressee_id' => $id])
                ->andWhere(['in','type', [
                    CommunicationTypes::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_PROJECT_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_SEGMENT_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_CONFIRM_SEGMENT_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_PROBLEM_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_CONFIRM_PROBLEM_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_GCP_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_CONFIRM_GCP_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_MVP_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_CONFIRM_MVP_EXPERTISE,
                    CommunicationTypes::USER_ALLOWED_BUSINESS_MODEL_EXPERTISE
                ]])
                ->orderBy('id DESC');

            $pages = new Pagination(['totalCount' => $query_communications->count(), 'page' => ($page - 1), 'pageSize' => $pageSize]);
            $pages->pageSizeParam = false; //убираем параметр $per-page
            $communications = $query_communications->offset($pages->offset)->limit($pageSize)->all();

            return $this->render('notifications', [
                'communications' => $communications,
                'pages' => $pages,
            ]);
        }

        return $this->goBack();
    }


    /**
     * Прочтение уведомлений
     * (коммуникации)
     * по проекту
     *
     * @param int $id
     * @return array|bool
     * @throws Throwable
     * @throws StaleObjectException
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
     * Прочтение уведомлений трекером
     * (дублирующие коммуникации)
     * по проекту
     *
     * @param int $id
     * @return array|bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionReadDuplicateCommunication(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $communication = DuplicateCommunications::findOne($id);
            $communication->setStatusRead();
            $communication->update();

            $user = User::findOne($communication->getAdresseeId());
            $countUnreadCommunications = $user->getCountUnreadCommunications();
            $countUnreadCommunicationsByProject = $user->getCountUnreadCommunicationsByProject($communication->source->getProjectId());

            $response = [
                'project_id' => $communication->source->getProjectId(),
                'countUnreadCommunications' => $countUnreadCommunications,
                'countUnreadCommunicationsByProject' => $countUnreadCommunicationsByProject
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }
}
