<?php

namespace app\controllers;

use app\models\ClientSettings;
use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\ContractorTasks;
use app\models\forms\CacheForm;
use app\models\forms\FormCreateSegment;
use app\models\forms\FormFilterRequirement;
use app\models\forms\FormUpdateSegment;
use app\models\forms\SearchForm;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\RequirementWishList;
use app\models\Roadmap;
use app\models\User;
use app\models\UserAccessToProjects;
use kartik\mpdf\Pdf;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use Throwable;
use Yii;
use app\models\Segments;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use app\models\SegmentSort;
use yii\web\Response;

/**
 * Контроллер с методами для создания, редактирования и получения информации по сегментам
 *
 * Class SegmentsController
 * @package app\controllers
 */
class SegmentsController extends AppUserPartController
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

        if (in_array($action->id, ['update', 'delete'])){

            $model = Segments::findOne((int)Yii::$app->request->get('id'));
            $project = Projects::findOne($model->getProjectId());

            if (($project->getUserId() === $currentUser->getId())){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif ($action->id === 'mpdf-segment'){

            /** @var $model Segments */
            $model = Segments::find(false)
                ->andWhere(['id' => (int)Yii::$app->request->get('id')])
                ->one();

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $model->getProjectId()])
                ->one();

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

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                    return parent::beforeAction($action);
                }

                PatternHttpException::noAccess();

            } elseif (User::isUserExpert($currentUser->getUsername())) {

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

                            PatternHttpException::noAccess();

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

        }elseif (in_array($action->id, ['index', 'mpdf-table-segments'])){

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => (int)Yii::$app->request->get('id')])
                ->one();

            if (!$project) {
                PatternHttpException::noData();
            }

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

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
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

        }elseif ($action->id === 'create'){

            $project = Projects::findOne((int)Yii::$app->request->get('id'));

            if (($project->getUserId() === $currentUser->getId())){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }else{
            return parent::beforeAction($action);
        }

    }


    /**
     * @param int $id
     * @return Response|string
     */
    public function actionIndex(int $id)
    {
        $countModels = Segments::find(false)
            ->andWhere(['project_id' => $id])
            ->count();

        if ((int)$countModels === 0) {
            return $this->redirect(['/segments/instruction', 'id' => $id]);
        }

        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        $models = Segments::findAll(['project_id' => $project->getId()]);
        $searchForm = new SearchForm();

        if ($searchForm->load(Yii::$app->request->post())) {
            $models = Segments::find()
                ->andWhere(['project_id' => $project->getId()])
                ->andWhere(['like', 'name', $searchForm->search])
                ->all();
        }

        $existTrashList = Segments::find(false)
            ->andWhere(['project_id' => $id])
            ->andWhere(['not', ['deleted_at' => null]])
            ->exists();

        $trashList = Segments::find(false)
            ->andWhere(['project_id' => $id])
            ->andWhere(['not', ['deleted_at' => null]])
            ->all();

        return $this->render('index', [
            'project' => $project,
            'models' => $models,
            'searchForm' => $searchForm,
            'existTrashList' => $existTrashList,
            'trashList' => $trashList
        ]);
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionList(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('_index_ajax', [
                    'models' => Segments::findAll(['project_id' => $id])
                ])];
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
    public function actionTrashList(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $queryModels = Segments::find(false)
                ->andWhere(['project_id' => $id])
                ->andWhere(['not', ['deleted_at' => null]]);

            $response = [
                'countItems' => $queryModels->count(),
                'renderAjax' => $this->renderAjax('_trash_ajax', [
                    'projectId' => $id,
                    'models' => $queryModels->all()
                ])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return Response|string
     */
    public function actionInstruction (int $id)
    {
        $countModels = Segments::find(false)
            ->andWhere(['project_id' => $id])
            ->count();

        if ((int)$countModels > 0) {
            return $this->redirect(['/segments/index', 'id' => $id]);
        }

        return $this->render('index_first', [
            'project' => Projects::findOne($id),
        ]);
    }


    /**
     * @return bool|string
     */
    public function actionGetInstruction ()
    {
        if(Yii::$app->request->isAjax) {
            $response = $this->renderAjax('instruction');
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param $current_id
     * @param $type_sort_id
     * @return array|bool
     */
    public function actionSortingModels($current_id, $type_sort_id)
    {
        $sort = new SegmentSort();

        if (Yii::$app->request->isAjax) {

            $response =  ['renderAjax' => $this->renderAjax('_index_ajax', [
                'models' => $sort->fetchModels($current_id, $type_sort_id)
                ])
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @param int|null $taskId
     * @return bool
     * @throws Throwable
     */
    public function actionSaveCacheCreationForm(int $id, int $taskId = null): bool
    {
        if(Yii::$app->request->isAjax) {

            try {
                if (!$taskId) {
                    $project = Projects::findOne($id);
                    $projectId = $project->getId();
                } else {
                    $task = ContractorTasks::findOne($taskId);
                    $projectId = $task->getProjectId();
                    if ($task->getStatus() === ContractorTasks::TASK_STATUS_NEW) {
                        $task->changeStatus(ContractorTasks::TASK_STATUS_PROCESS);
                    }
                }

            } catch (\Exception $exception) {
                return false;
            }

            $cachePath = FormCreateSegment::getCachePath($projectId);
            $cacheName = 'formCreateHypothesisCache';

            $cache = new CacheForm();
            $cache->setCache($cachePath, $cacheName);
        }
        return false;
    }


    /**
     * @param int $id
     * @param bool|null $useWishList
     * @param int|null $requirementId
     * @return array|bool
     */
    public function actionGetHypothesisToCreate(int $id, bool $useWishList = null, int $requirementId = null)
    {
        $project = Projects::findOne($id);
        $model = new FormCreateSegment($project, $useWishList, $requirementId);

        if ($requirementId) {
            $requirement = RequirementWishList::findOne($requirementId);
            $wishList = $requirement->wishList;
            $model->setFieldOfActivityB2b($wishList->getCompanyFieldOfActivity());
            $model->setSortOfActivityB2b($wishList->getCompanySortOfActivity());
            $model->setCompanyProducts($wishList->getCompanyProducts());
        }

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('create', [
                    'model' => $model,
                    'project' => $project
                ]),
            ];

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $projectId
     * @param int $page
     * @return array|false
     */
    public function actionGetListRequirements(int $projectId, int $page = 1)
    {
        if(Yii::$app->request->isAjax) {

            $user = User::findOne(Yii::$app->user->getId());
            $client = $user->clientUser->client;
            $wishLists = $client->findWishLists();
            $wishListIds = array_column($wishLists, 'id');
            $query = RequirementWishList::find()
                ->leftJoin('wish_list', '`wish_list`.`id` = `requirement_wish_list`.`wish_list_id`')
                ->andWhere(['in', 'wish_list_id', $wishListIds])
                ->andWhere(['is_actual' => RequirementWishList::REQUIREMENT_ACTUAL]);

            $filters = new FormFilterRequirement();
            if ($filters->load(Yii::$app->request->post())) {
                if ($filters->getRequirement()) {
                    $query = $query->andWhere(['like', 'requirement', $filters->getRequirement()]);
                }
                if ($filters->getReason()) {
                    $query = $query->innerJoin('reason_requirement_wish_list', '`reason_requirement_wish_list`.`requirement_wish_list_id` = `requirement_wish_list`.`id`')
                        ->andWhere(['like', 'reason_requirement_wish_list.reason', $filters->getReason()]);
                }
                if ($filters->getExpectedResult()) {
                    $query = $query->andWhere(['like', 'expected_result', $filters->getExpectedResult()]);
                }
                if ($filters->getFieldOfActivity()) {
                    $query = $query->andWhere(['like', 'wish_list.company_field_of_activity', $filters->getFieldOfActivity()]);
                }
                if ($filters->getSortOfActivity()) {
                    $query = $query->andWhere(['like', 'wish_list.company_sort_of_activity', $filters->getSortOfActivity()]);
                }
                if ($filters->getSize()) {
                    $query = $query->andWhere(['wish_list.size' => (int)$filters->getSize()]);
                }
                if ($filters->getLocationId()) {
                    $query = $query->andWhere(['wish_list.location_id' => (int)$filters->getLocationId()]);
                }
                if ($filters->getTypeCompany()) {
                    $query = $query->andWhere(['wish_list.type_company' => (int)$filters->getTypeCompany()]);
                }
                if ($filters->getTypeProduction()) {
                    $query = $query->andWhere(['wish_list.type_production' => (int)$filters->getTypeProduction()]);
                }
            }

            $limit = 20;
            $pages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => $limit]);
            $pages->pageSizeParam = false; //убираем параметр $per-page
            $requirements = $query->offset($pages->offset)->limit($limit)->all();

            $response = [
                'renderAjax' => $this->renderAjax('list_requirements', [
                    'requirements' => $requirements,
                    'projectId' => $projectId,
                    'filters' => $filters,
                    'pages' => $pages
                ])
            ];

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function actionCreate(int $id)
    {
        $project = Projects::findOne($id);
        $model = new FormCreateSegment($project);
        $model->project_id = $id;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            if ($model->checkFillingFields()) {

                if ($model->validate(['name'])) {

                    if ($model->create()) {

                        $response =  [
                            'success' => true,
                            'count' => Segments::find(false)->andWhere(['project_id' => $id])->count(),
                            'renderAjax' => $this->renderAjax('_index_ajax', [
                                'models' => Segments::findAll(['project_id' => $id]),
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                }else {

                    //Сегмент с таким именем уже существует
                    $response =  ['segment_already_exists' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

            } else {

                //Данные не загружены
                $response =  ['data_not_loaded' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetHypothesisToUpdate (int $id)
    {
        $model = new FormUpdateSegment($id);

        if(Yii::$app->request->isAjax) {

            $response = [
                'model' => $model,
                'renderAjax' => $this->renderAjax('update', ['model' => $model]),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $segment = $this->findModel($id);
        $project = Projects::findOne(['id' => $segment->getProjectId()]);
        $model = new FormUpdateSegment($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            if ($model->checkFillingFields()) {

                if ($model->validate(['name'])) {

                    if ($model->update()) {

                        $response =  [
                            'success' => true,
                            'renderAjax' => $this->renderAjax('_index_ajax', [
                                'models' => Segments::findAll(['project_id' => $project->id]),
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }else {

                    //Сегмент с таким именем уже существует
                    $response =  ['segment_already_exists' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            } else {

                //Данные не загружены
                $response =  ['data_not_loaded' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * Включить разрешение на экспертизу
     * @param int $id
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionEnableExpertise(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $segment = Segments::findOne($id);
            if ($segment->allowExpertise()) {

                $response = [
                    'success' => true,
                    'renderAjax' => $this->renderAjax('_index_ajax', [
                        'models' => Segments::findAll(['project_id' => $segment->getProjectId()]),
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @return array
     */
    public function actionListTypeSort(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (isset($_POST['depdrop_parents'])) {

            $parents = $_POST['depdrop_parents'];

            if ($parents != null && $parents[0] != 0) {

                $cat_id = $parents[0];
                $out = SegmentSort::getListTypes($cat_id);
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionShowAllInformation (int $id)
    {
        /** @var $segment Segments */
        $segment = Segments::find(false)
            ->andWhere(['id' => $id])
            ->one();

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('all-information', ['segment' => $segment]),
                'segment' => $segment,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return mixed
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws InvalidConfigException
     */
    public function actionMpdfSegment (int $id)
    {
        /** @var $model Segments */
        $model = Segments::find(false)
            ->andWhere(['id' => $id])
            ->one();

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('mpdf_segment', ['segment' => $model]);

        $destination = Pdf::DEST_BROWSER;
        //$destination = Pdf::DEST_DOWNLOAD;

        $filename = 'Сегмент «'.$model->getName() .'».pdf';

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
            'cssFile' => '@app/web/css/mpdf-hypothesis-style.css',
            'marginFooter' => 5,
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' => [$model->getName()],
                'SetHeader' => ['<div style="color: #3c3c3c;">Сегмент «'.$model->getName().'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
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
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws InvalidConfigException
     */
    public function actionMpdfTableSegments(int $id)
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $models Segments[] */
        $models = !$project->getDeletedAt() ?
            $project->segments :
            Segments::find(false)
                ->andWhere(['project_id' => $id])
                ->all();

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('mpdf_table_segments', ['models' => $models]);

        $destination = Pdf::DEST_BROWSER;
        //$destination = Pdf::DEST_DOWNLOAD;

        $filename = 'Сегменты проекта «'.$project->getProjectName() .'».pdf';

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
            'cssFile' => '@app/web/css/mpdf-index-table-hypothesis-style.css',
            'marginFooter' => 5,
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' => ['Сегменты проекта «'.$project->getProjectName() .'»'],
                'SetHeader' => ['<div style="color: #3c3c3c;">Сегменты проекта «'.$project->getProjectName().'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
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
     * @return array|bool
     */
    public function actionShowRoadmap (int $id)
    {
        $roadmap = new Roadmap($id);
        $segment = Segments::find(false)
            ->andWhere(['id' => $id])
            ->one();

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('roadmap', ['roadmap' => $roadmap]),
                'segment' => $segment,
                ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return bool
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionDelete(int $id): bool
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->isAjax && $model->softDeleteStage()) {
            return true;
        }
        return false;
    }


    /**
     * @param int $id
     * @return void|Response
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionRecovery(int $id)
    {
        $model = $this->findModel($id, false);

        if($model->recoveryStage()) {
            return $this->redirect(['index', 'id' => $model->getProjectId()]);
        }

        PatternHttpException::noData();
    }


    /**
     * Finds the Segments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return Segments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): Segments
    {
        if (!$isOnlyNotDelete) {
            $model = Segments::find(false)
                ->andWhere(['id' => $id])
                ->one();

        } else {
            $model = Segments::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
