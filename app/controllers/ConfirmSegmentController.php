<?php

namespace app\controllers;

use app\models\ClientSettings;
use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\EnableExpertise;
use app\models\forms\CacheForm;
use app\models\forms\FormCreateConfirmDescription;
use app\models\forms\FormCreateConfirmSegment;
use app\models\forms\FormCreateProblem;
use app\models\forms\FormCreateQuestion;
use app\models\forms\FormUpdateConfirmDescription;
use app\models\forms\FormUpdateConfirmSegment;
use app\models\forms\SearchForm;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\QuestionsConfirmSegment;
use app\models\RespondsSegment;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use app\models\UserAccessToProjects;
use kartik\mpdf\Pdf;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Контроллер с методами для создания,
 * редактирования и получения информации по этапу подтверждения сегмента
 *
 * Class ConfirmSegmentController
 * @package app\controllers
 */
class ConfirmSegmentController extends AppUserPartController
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

        // Закомментировал потому, что блокируются методы для гипотез которые находятся в корзине, исправить это.
        if ($action->id === 'view'/*, 'mpdf-questions-and-answers', 'mpdf-data-responds'*/){

            $confirm = $this->findModel((int)Yii::$app->request->get('id'), false);

            if ($confirm->getDeletedAt()) {
                return parent::beforeAction($action);
            }

            $hypothesis = $confirm->hypothesis;
            $project = $hypothesis->project;

            if (($project->getUserId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            if (User::isUserAdmin($currentUser->getUsername()) && $project->user->getIdAdmin() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $project->user->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE && !User::isUserAdminCompany($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }

                PatternHttpException::noAccess();
            }

            if (User::isUserExpert($currentUser->getUsername())) {

                $expert = User::findOne(Yii::$app->user->getId());

                /** @var UserAccessToProjects $userAccessToProject */
                $userAccessToProject = $expert->findUserAccessToProject($project->getId());

                if ($userAccessToProject) {

                    if ($userAccessToProject->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) {

                        $responsiveCommunication = $userAccessToProject->communication->responsiveCommunication;

                        if ($responsiveCommunication) {

                            if ($responsiveCommunication->communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) {
                                return parent::beforeAction($action);
                            }

                        } elseif (time() < $userAccessToProject->getDateStop()) {
                            return parent::beforeAction($action);
                        }
                        PatternHttpException::noAccess();

                    } elseif ($userAccessToProject->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) {

                        return parent::beforeAction($action);

                    } else {
                        PatternHttpException::noAccess();
                    }
                } else{
                    PatternHttpException::noAccess();
                }

            } else{
                PatternHttpException::noAccess();
            }

        }elseif ($action->id === 'update'){

            $confirm = ConfirmSegment::findOne((int)Yii::$app->request->get('id'));
            $hypothesis = $confirm->hypothesis;
            $project = $hypothesis->project;

            if ($project->getUserId() === $currentUser->getId()){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif ($action->id === 'create'){

            $hypothesis = Segments::findOne((int)Yii::$app->request->get('id'));
            if (!$hypothesis) {
                PatternHttpException::noData();
            }
            $project = $hypothesis->project;

            if ($project->getUserId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif ($action->id === 'save-confirm'){

            $hypothesis = Segments::findOne((int)Yii::$app->request->get('id'));
            $project = $hypothesis->project;

            if ($project->getUserId() === $currentUser->getId()) {
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'add-questions'){

            $confirm = ConfirmSegment::findOne((int)Yii::$app->request->get('id'));
            if (!$confirm) {
                PatternHttpException::noData();
            }
            $hypothesis = $confirm->hypothesis;
            $project = $hypothesis->project;

            if (($project->getUserId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            if (User::isUserAdmin($currentUser->getUsername()) && $project->user->getIdAdmin() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $project->user->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE && !User::isUserAdminCompany($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }

                PatternHttpException::noAccess();
            }

            if (User::isUserExpert($currentUser->getUsername())) {

                $expert = User::findOne(Yii::$app->user->getId());
                $userAccessToProject = $expert->findUserAccessToProject($project->getId());

                /** @var UserAccessToProjects $userAccessToProject */
                if ($userAccessToProject) {

                    if ($userAccessToProject->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) {

                        $responsiveCommunication = $userAccessToProject->communication->responsiveCommunication;

                        if ($responsiveCommunication) {

                            if ($responsiveCommunication->communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) {
                                return parent::beforeAction($action);
                            }

                        } elseif (time() < $userAccessToProject->getDateStop()) {
                            return parent::beforeAction($action);
                        }

                        PatternHttpException::noAccess();

                    } elseif ($userAccessToProject->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) {

                        return parent::beforeAction($action);

                    } else {
                        PatternHttpException::noAccess();
                    }
                } else{
                    PatternHttpException::noAccess();
                }

            } else{
                PatternHttpException::noAccess();
            }

        }else{
            return parent::beforeAction($action);
        }
    }


    /**
     * @param int $id
     * @param bool $existDesc
     */
    public function actionSaveCacheCreationForm(int $id, bool $existDesc = false): void
    {
        $segment = Segments::findOne($id);
        if (!$existDesc) {
            $cachePath = FormCreateConfirmSegment::getCachePath($segment);
            $cacheName = 'formCreateConfirmCache';
        } else {
            $cachePath = FormCreateConfirmDescription::getCachePath($segment);
            $cacheName = 'formCreateConfirmDCache';
        }

        if(Yii::$app->request->isAjax) {

            $cache = new CacheForm();
            $cache->setCache($cachePath, $cacheName);
        }
    }


    /**
     * @param int $id
     * @param bool $existDesc
     * @return string|Response
     */
    public function actionCreate(int $id, bool $existDesc = false)
    {
        $segment = Segments::findOne($id);
        $project = Projects::findOne($segment->getProjectId());
        $model = new FormCreateConfirmSegment($segment);

        if ($segment->getEnableExpertise() === EnableExpertise::OFF) {
            return $this->redirect(['/segments/index', 'id' => $project->getId()]);
        }

        if ($segment->confirm) {
            // Если у сегмента создана программа подтверждения,
            // то перейти на страницу подтверждения
            return $this->redirect(['view', 'id' => $segment->confirm->getId()]);
        }

        if ($existDesc) {
            $confirm = new ConfirmSegment();
            $confirm->setSegmentId($segment->getId());
            $model = new FormCreateConfirmDescription($confirm, StageExpertise::CONFIRM_SEGMENT);

            return $this->render('create_for_exist', [
                'model' => $model,
                'segment' => $segment,
                'project' => $project,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'segment' => $segment,
            'project' => $project,
        ]);
    }


    /**
     * @param int $id
     * @param bool $existDesc
     * @return array|bool
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function actionSaveConfirm(int $id, bool $existDesc = false)
    {
        $segment = Segments::findOne($id);

        if (!$existDesc) {
            $model = new FormCreateConfirmSegment($segment);
            $model->setHypothesisId($id);

            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                if ($model = $model->create()) {
                    $response =  ['success' => true, 'id' => $model->getId()];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }

            return false;
        }

        $confirm = new ConfirmSegment();
        $confirm->setSegmentId($segment->getId());
        $model = new FormCreateConfirmDescription($confirm, StageExpertise::CONFIRM_SEGMENT);

        if ($model->setParams(Yii::$app->request->post())) {
            if (!$model->validate()) {
                $errors = [];
                foreach ($model->errors as $param) {
                    foreach ($param as $error) {
                        $errors[] = $error;
                    }
                }

                $response =  ['success' => false, 'errors' => $errors];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            if (Yii::$app->request->isAjax && $result = $model->create()) {
                $response =  ['success' => true, 'id' => $result->confirm->getId()];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }

    /**
     * Страница со списком вопросов
     *
     * @param int $id
     * @return string
     */
    public function actionAddQuestions(int $id): string
    {
        $model = ConfirmSegment::findOne($id);
        $formUpdateConfirmSegment = new FormUpdateConfirmSegment($id);
        $segment = Segments::findOne($model->getSegmentId());
        $project = Projects::findOne($segment->getProjectId());
        $questions = QuestionsConfirmSegment::findAll(['confirm_id' => $id]);
        $newQuestion = new FormCreateQuestion();
        $countContractorResponds = (int)RespondsSegment::find()
            ->andWhere(['not', ['contractor_id' => null]])
            ->andWhere(['confirm_id' => $id])
            ->count();

        //Список вопросов для добавления к списку программы
        $queryQuestions = $model->queryQuestionsGeneralList();
        $queryQuestions = ArrayHelper::map($queryQuestions,'title','title');

        return $this->render('add-questions', [
            'questions' => $questions,
            'newQuestion' => $newQuestion,
            'queryQuestions' => $queryQuestions,
            'model' => $model,
            'formUpdateConfirmSegment' => $formUpdateConfirmSegment,
            'segment' => $segment,
            'project' => $project,
            'countContractorResponds' => $countContractorResponds
        ]);
    }


    /**
     * @param int $id
     * @param bool $existDesc
     * @return array|bool
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function actionUpdate (int $id, bool $existDesc = false)
    {
        $confirm = ConfirmSegment::findOne($id);
        $segment = Segments::findOne($confirm->getSegmentId());
        $project = Projects::findOne($segment->getProjectId());

        if (!$existDesc) {
            $model = new FormUpdateConfirmSegment($id);
            $countContractorResponds = (int)RespondsSegment::find()
                ->andWhere(['not', ['contractor_id' => null]])
                ->andWhere(['confirm_id' => $id])
                ->count();

            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                if ($confirm = $model->update()) {
                    $response = [
                        'success' => true,
                        'ajax_data_confirm' => $this->renderAjax('ajax_data_confirm', [
                            'formUpdateConfirmSegment' => new FormUpdateConfirmSegment($id),
                            'model' => $confirm, 'project' => $project, 'countContractorResponds' => $countContractorResponds
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }

            return false;
        }

        $model = new FormUpdateConfirmDescription($confirm, StageExpertise::CONFIRM_SEGMENT);
        if ($model->setParams(Yii::$app->request->post())) {
            if (!$model->validate()) {
                $errors = [];
                foreach ($model->errors as $param) {
                    foreach ($param as $error) {
                        $errors[] = $error;
                    }
                }

                $response =  ['success' => false, 'errors' => $errors];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            if (Yii::$app->request->isAjax && $result = $model->update()) {
                $response =  ['success' => true, 'id' => $result->confirm->getId()];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $model = $this->findModel($id, false);
        if ($model->getDeletedAt()) {
            return $this->redirect(['/confirm-segment/view-trash', 'id' => $id]);
        }

        $segment = Segments::findOne($model->getSegmentId());
        $project = Projects::findOne($segment->getProjectId());

        if ($model->isExistDesc()) {
            $formUpdate = new FormUpdateConfirmDescription($model, StageExpertise::CONFIRM_SEGMENT);
            return $this->render('view_for_exist', [
                'model' => $model,
                'formUpdate' => $formUpdate,
                'segment' => $segment,
                'project' => $project
            ]);
        }

        $formUpdateConfirmSegment = new FormUpdateConfirmSegment($id);
        $questions = QuestionsConfirmSegment::findAll(['confirm_id' => $id]);
        $newQuestion = new FormCreateQuestion();
        $countContractorResponds = (int)RespondsSegment::find()
            ->andWhere(['not', ['contractor_id' => null]])
            ->andWhere(['confirm_id' => $id])
            ->count();

        //Список вопросов для добавления к списку программы
        $queryQuestions = $model->queryQuestionsGeneralList();
        $queryQuestions = ArrayHelper::map($queryQuestions,'title','title');


        return $this->render('view', [
            'model' => $model,
            'formUpdateConfirmSegment' => $formUpdateConfirmSegment,
            'segment' => $segment,
            'project' => $project,
            'questions' => $questions,
            'newQuestion' => $newQuestion,
            'queryQuestions' => $queryQuestions,
            'searchForm' => new SearchForm(),
            'countContractorResponds' => $countContractorResponds
        ]);
    }


    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewTrash(int $id): string
    {
        /**
         * @var $model ConfirmSegment
         * @var $segment Segments
         * @var $project Projects
         * @var $questions QuestionsConfirmSegment[]
         */
        $model = $this->findModel($id, false);

        $segment = Segments::find(false)
            ->andWhere(['id' => $model->getSegmentId()])
            ->one();

        $project = Projects::find(false)
            ->andWhere(['id' => $segment->getProjectId()])
            ->one();

        if ($model->isExistDesc()) {
            return $this->render('view_for_exist', [
                'model' => $model,
                'segment' => $segment,
                'project' => $project
            ]);
        }

        $questions = QuestionsConfirmSegment::find(false)
            ->andWhere(['confirm_id' => $id])
            ->all();

        $countContractorResponds = (int)RespondsSegment::find(false)
            ->andWhere(['not', ['contractor_id' => null]])
            ->andWhere(['confirm_id' => $id])
            ->count();

        return $this->render('view_trash', [
            'model' => $model,
            'segment' => $segment,
            'project' => $project,
            'questions' => $questions,
            'searchForm' => new SearchForm(),
            'countContractorResponds' => $countContractorResponds
        ]);
    }


    /**
     * @return bool|string
     */
    public function actionGetInstructionStepOne ()
    {
        if(Yii::$app->request->isAjax) {
            $response = $this->renderAjax('instruction_step_one');
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @return bool|string
     */
    public function actionGetInstructionStepTwo ()
    {
        if(Yii::$app->request->isAjax) {
            $response = $this->renderAjax('instruction_step_two');
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @return bool|string
     */
    public function actionGetInstructionStepThree ()
    {
        if(Yii::$app->request->isAjax) {
            $response = $this->renderAjax('instruction_step_three');
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Проверка данных подтверждения на этапе генерации ГПС
     *
     * @param int $id
     * @return array|bool
     */
    public function actionDataAvailabilityForNextStep(int $id)
    {
        $model = ConfirmSegment::findOne($id);
        $formCreateProblem = new FormCreateProblem($model->hypothesis);

        $count_descInterview = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $id])->andWhere(['not', ['interview_confirm_segment.id' => null]])->count();

        $count_positive = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $id, 'interview_confirm_segment.status' => '1'])->count();

        if (Yii::$app->request->isAjax) {

            if ($model->isExistDesc()) {
                $response =  [
                    'success' => true,
                    'cacheExpectedResultsInterview' => $formCreateProblem->_cacheManager->getCache($formCreateProblem->cachePath, 'formCreateHypothesisCache')['FormCreateProblem']['_expectedResultsInterview'],
                    'renderAjax' => $this->renderAjax('/problems/create', [
                        'confirmSegment' => $model,
                        'model' => $formCreateProblem,
                        'responds' => [],
                    ]),
                ];

            } elseif (($model->problems  && $model->getCountPositive() <= $count_positive && $model->hypothesis->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) || (count($model->responds) === $count_descInterview && $model->getCountPositive() <= $count_positive && $model->hypothesis->getExistConfirm() === StatusConfirmHypothesis::COMPLETED)) {

                $response =  [
                    'success' => true,
                    'cacheExpectedResultsInterview' => $formCreateProblem->_cacheManager->getCache($formCreateProblem->cachePath, 'formCreateHypothesisCache')['FormCreateProblem']['_expectedResultsInterview'],
                    'renderAjax' => $this->renderAjax('/problems/create', [
                        'confirmSegment' => $model,
                        'model' => $formCreateProblem,
                        'responds' => RespondsSegment::find()->with('interview')
                            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
                            ->andWhere(['confirm_id' => $id, 'interview_confirm_segment.status' => '1'])->all(),
                    ]),
                ];

            } else{
                $response = ['error' => true];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }

    /**
     * Завершение подтверждения сегмента и переход на следующий этап
     *
     * @param int $id
     * @return array|bool
     */
    public function actionMovingNextStage(int $id)
    {
        $model = ConfirmSegment::findOne($id);
        $segment = $model->segment;

        $count_descInterview = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $id])->andWhere(['not', ['interview_confirm_segment.id' => null]])->count();

        $count_positive = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $id, 'interview_confirm_segment.status' => '1'])->count();

        $existTasksNotCompleted = ContractorTasks::find()
            ->andWhere(['type' => StageExpertise::CONFIRM_SEGMENT])
            ->andWhere(['hypothesis_id' => $model->getId()])
            ->andWhere(['in', 'status', [
                ContractorTasks::TASK_STATUS_NEW,
                ContractorTasks::TASK_STATUS_PROCESS,
                ContractorTasks::TASK_STATUS_COMPLETED,
                ContractorTasks::TASK_STATUS_RETURNED
            ]])
            ->exists();


        if(Yii::$app->request->isAjax) {

            if (!$model->problems && $existTasksNotCompleted) {

                $response = ['not_completed_descInterviews' => true];

            } elseif (!$model->problems && count($model->responds) > $count_descInterview) {

                $response = ['not_completed_descInterviews' => true];

            } elseif ($model->problems || (count($model->responds) === $count_descInterview && $model->getCountPositive() <= $count_positive)) {

                $response =  ['success' => true, 'exist_confirm' => $segment->getExistConfirm()];

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
     * @return bool|Response
     * @throws ErrorException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionNotExistConfirm(int $id)
    {
        $model = $this->findModel($id);
        $segment = Segments::findOne($model->getSegmentId());
        $project = Projects::findOne($segment->getProjectId());
        $cacheManager = new CacheForm();
        $cachePath = $model->getCachePath();

        if ($segment->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
            return $this->redirect(['/segments/index', 'id' => $project->getId()]);
        }

        $segment->setExistConfirm(StatusConfirmHypothesis::NOT_COMPLETED);
        $segment->setTimeConfirm();

        if ($model->allowExpertise($segment)){

            $cacheManager->deleteCache($cachePath); // Удаление дирректории для кэша подтверждения
            $segment->trigger(Segments::EVENT_CLICK_BUTTON_CONFIRM);
            return $this->redirect(['/segments/index', 'id' => $project->getId()]);
        }
        return false;
    }

    /**
     * @param int $id
     * @return bool|Response
     * @throws ErrorException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionExistConfirm(int $id)
    {
        $model = $this->findModel($id);
        $segment = Segments::findOne($model->getSegmentId());
        $cacheManager = new CacheForm();
        $cachePath = $model->getCachePath();

        $segment->setExistConfirm(StatusConfirmHypothesis::COMPLETED);
        $segment->setTimeConfirm();

        if ($model->allowExpertise($segment)){

            $cacheManager->deleteCache($cachePath); // Удаление дирректории для кэша подтверждения
            $segment->trigger(Segments::EVENT_CLICK_BUTTON_CONFIRM);
            return $this->redirect(['/problems/index', 'id' => $id]);
        }
        return false;
    }


    /**
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetDataQuestionsAndAnswers(int $id, bool $isOnlyNotDelete = true): array
    {
        $model = $this->findModel($id, $isOnlyNotDelete);

        /** @var $questions QuestionsConfirmSegment[] */
        $questions = $isOnlyNotDelete ?
            $model->questions :
            QuestionsConfirmSegment::find(false)
                ->andWhere(['confirm_id' => $model->getId()])
                ->all();

        $response = ['ajax_questions_and_answers' => $this->renderAjax('ajax_questions_and_answers', [
            'questions' => $questions, 'isOnlyNotDelete' => $isOnlyNotDelete
        ])];
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $response;
        return $response;

    }


    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws InvalidConfigException
     */
    public function actionMpdfQuestionsAndAnswers(int $id)
    {
        $model = $this->findModel($id, false);
        /** @var $questions QuestionsConfirmSegment[] */
        $questions = QuestionsConfirmSegment::find(false)
            ->andWhere(['confirm_id' => $model->getId()])
            ->all();

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('questions_and_answers_pdf', ['questions' => $questions]);

        $destination = Pdf::DEST_BROWSER;
        //$destination = Pdf::DEST_DOWNLOAD;

        /** @var $segment Segments */
        $segment = Segments::find(false)
            ->andWhere(['id' => $model->getSegmentId()])
            ->one();

        $segment_name = $segment->getName();
        if (mb_strlen($segment_name) > 25) {
            $segment_name = mb_substr($segment_name, 0, 25) . '...';
        }

        $filename = 'Ответы респондентов на вопросы интервью для подтверждения сегмента: «'.$segment_name.'».';

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            //'format' => Pdf::FORMAT_TABLOID,
            // portrait orientation
            //'orientation' => Pdf::ORIENT_LANDSCAPE,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => $destination,
            'filename' => $filename,
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssFile' => '@app/web/css/style.css',
            // any css to be embedded if required
            //'cssInline' => '.business-model-view-export {color: #3c3c3c;};',
            'marginTop' => 20,
            'marginBottom' => 20,
            'marginFooter' => 5,
            'defaultFont' => 'RobotoCondensed-Light',
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' => $filename,
                'SetHeader' => ['<div style="color: #3c3c3c;">Ответы респондентов на вопросы интервью. Сегмент: «'.$segment_name.'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
                'SetFooter' => ['<div style="color: #3c3c3c;">Страница {PAGENO}</div>'],
                //'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                //'SetAuthor' => 'Kartik Visweswaran',
                //'SetCreator' => 'Kartik Visweswaran',
                //'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }


    /**
     * @param int $id
     * @return mixed
     * @throws CrossReferenceException
     * @throws InvalidConfigException
     * @throws MpdfException
     * @throws NotFoundHttpException
     * @throws PdfParserException
     * @throws PdfTypeException
     */
    public function actionMpdfDataResponds(int $id)
    {
        $model = $this->findModel($id, false);

        /** @var $responds RespondsSegment[] */
        $responds = RespondsSegment::find(false)
            ->andWhere(['confirm_id' => $model->getId()])
            ->all();

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('viewpdf', ['model' => $model, 'responds' => $responds]);

        $destination = Pdf::DEST_BROWSER;
        //$destination = Pdf::DEST_DOWNLOAD;

        /** @var $segment Segments */
        $segment = Segments::find(false)
            ->andWhere(['id' => $model->getSegmentId()])
            ->one();

        $segment_name = $segment->getName();
        if (mb_strlen($segment_name) > 25) {
            $segment_name = mb_substr($segment_name, 0, 25) . '...';
        }

        $filename = 'Подтверждение сегмента «'.$segment_name.'». Таблица респондентов.pdf';

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            //'format' => Pdf::FORMAT_TABLOID,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            //'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => $destination,
            'filename' => $filename,
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssFile' => '@app/web/css/style.css',
            // any css to be embedded if required
            //'cssInline' => '.business-model-view-export {color: #3c3c3c;};',
            'marginFooter' => 5,
            'defaultFont' => 'RobotoCondensed-Light',
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' => ['Респонденты для подтверждения гипотезы сегмента «'.$segment_name.'»'],
                'SetHeader' => ['<div style="color: #3c3c3c;">Респонденты для подтверждения гипотезы сегмента «'.$segment_name.'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
                'SetFooter' => ['<div style="color: #3c3c3c;">Страница {PAGENO}</div>'],
                //'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                //'SetAuthor' => 'Kartik Visweswaran',
                //'SetCreator' => 'Kartik Visweswaran',
                //'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
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
