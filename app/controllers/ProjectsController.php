<?php


namespace app\controllers;

use app\models\Authors;
use app\models\BusinessModel;
use app\models\ClientSettings;
use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\ContractorProjectAccess;
use app\models\forms\CacheForm;
use app\models\forms\SearchForm;
use app\models\Gcps;
use app\models\Mvps;
use app\models\PatternHttpException;
use app\models\PreFiles;
use app\models\Problems;
use app\models\ProjectSort;
use app\models\Roadmap;
use app\models\Segments;
use app\models\User;
use app\models\UserAccessToProjects;
use kartik\mpdf\Pdf;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use Throwable;
use Yii;
use app\models\Projects;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


/**
 * Контроллер с методами создания,
 * редактирования и получения информации по проектам
 *
 * Class ProjectsController
 * @package app\controllers
 */
class ProjectsController extends AppUserPartController
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

        if (in_array($action->id, ['result', 'result-export', 'report', 'upshot', 'mpdf-project'])){

            /** @var $model Projects */
            $model = Projects::find(false)
                ->andWhere(['id' => (int)Yii::$app->request->get('id')])
                ->one();

            if (($model->getUserId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            if (User::isUserAdmin($currentUser->getUsername()) && $model->user->getIdAdmin() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $model->user->clientUser;

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
                $userAccessToProject = $expert->findUserAccessToProject($model->getId());

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

        }elseif ($action->id === 'mpdf-business-model'){

            $businessModel = BusinessModel::findOne((int)Yii::$app->request->get());
            $model = $businessModel->project;

            if (($model->getUserId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            if (User::isUserAdmin($currentUser->getUsername()) && $model->user->getIdAdmin() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $model->user->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                    return parent::beforeAction($action);
                }

            }

            PatternHttpException::noAccess();

        }elseif ($action->id === 'create'){

            if (User::isUserSimple($currentUser->getUsername()) && $currentUser->getId() === (int)Yii::$app->request->get('id')) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif (in_array($action->id, ['index', 'results'])){

            $model = User::findOne((int)Yii::$app->request->get('id'));
            if (!$model) {
                PatternHttpException::noData();
            }

            if (($model->getId() === $currentUser->getId())) {
                return parent::beforeAction($action);
            }

            if (User::isUserAdmin($currentUser->getUsername()) && $model->getIdAdmin() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $model->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                    return parent::beforeAction($action);
                }

                PatternHttpException::noAccess();

            } elseif (User::isUserExpert($currentUser->getUsername())) {

                $expert = User::findOne(Yii::$app->user->getId());

                if ((int)Yii::$app->request->get('project_id')) {

                    /** @var UserAccessToProjects $userAccessToProject */
                    $userAccessToProject = $expert->findUserAccessToProject((int)Yii::$app->request->get('project_id'));

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
            } elseif (User::isUserContractor($currentUser->getUsername())) {

                $project = Projects::findOne((int)Yii::$app->request->get('project_id'));
                $contractor = User::findOne(Yii::$app->user->getId());
                if ($contractor && $project && ContractorProjectAccess::existAccessByParams($contractor->getId(), $project->getId())) {
                    return parent::beforeAction($action);
                }

                PatternHttpException::noAccess();
            } else{
                PatternHttpException::noAccess();
            }

        }elseif (in_array($action->id, ['update', 'delete'])){

            $project = Projects::findOne((int)Yii::$app->request->get('id'));
            $user = User::findOne($project->getUserId());

            if (($user->getId() === $currentUser->getId())){

                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }else{
            return parent::beforeAction($action);
        }
    }


    /**
     * @param int $id
     * @param int|null $project_id
     * @return Response|string
     */
    public function actionIndex(int $id, int $project_id = null)
    {
        $user = User::findOne($id);
        $condition = $project_id ? ['user_id' => $id, 'id' => $project_id] : ['user_id' => $id];
        $countModels = Projects::find(false)
            ->andWhere($condition)
            ->count();

        if ((int)$countModels === 0) {
            return $this->redirect(['/projects/instruction', 'id' => $id]);
        }

        $models = Projects::findAll($condition);
        if ($project_id && User::isUserExpert(Yii::$app->user->identity['username'])) {
            /** @var $models Projects[] */
            $models = Projects::find(false)
                ->andWhere($condition)
                ->all();
        }

        $searchForm = new SearchForm();

        if ($searchForm->load(Yii::$app->request->post())) {

            $models = Projects::find()
                ->andWhere($condition)
                ->andWhere(['or',
                    ['like', 'project_name', $searchForm->search],
                    ['like', 'project_fullname', $searchForm->search],
                ])->all();
        }

        $existTrashList = Projects::find(false)
            ->andWhere(['user_id' => $id])
            ->andWhere(['not', ['deleted_at' => null]])
            ->exists();

        $trashList = Projects::find(false)
            ->andWhere(['user_id' => $id])
            ->andWhere(['not', ['deleted_at' => null]])
            ->all();

        return $this->render('index', [
            'user' => $user,
            'models' => $models,
            'new_author' => new Authors(),
            'searchForm' => new SearchForm(),
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
                    'models' => Projects::findAll(['user_id' => $id])
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

            $queryModels = Projects::find(false)
                ->andWhere(['user_id' => $id])
                ->andWhere(['not', ['deleted_at' => null]]);

            $response = [
                'countItems' => $queryModels->count(),
                'renderAjax' => $this->renderAjax('_trash_ajax', [
                    'userId' => $id,
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
    public function actionInstruction(int $id)
    {
        $models = Projects::findAll(['user_id' => $id]);
        if ($models) {
            return $this->redirect(['/projects/index', 'id' => $id]);
        }

        return $this->render('index_first', [
            'user' => User::findOne($id),
            'new_author' => new Authors(),
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
     * @param int $id
     */
    public function actionSaveCacheCreationForm(int $id): void
    {
        $user = User::findOne($id);
        $cachePath = Projects::getCachePath($user);
        $cacheName = 'formCreateProjectCache';

        if(Yii::$app->request->isAjax) {

            $cache = new CacheForm();
            $cache->setCache($cachePath, $cacheName);
        }
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetHypothesisToCreate(int $id)
    {
        $user = User::findOne($id);
        $model = new Projects();
        $author = new Authors();

        if(Yii::$app->request->isAjax) {

            $cachePath = $model::getCachePath($user);
            $cacheName = 'formCreateProjectCache';

            if (!($cache = $model->_cacheManager->getCache($cachePath, $cacheName))) {

                $response = [
                    'renderAjax' => $this->renderAjax('create', [
                        'user' => $user,
                        'model' => $model,
                        'author' => $author
                    ]),
                ];
            } else {

                //Заполнение полей модели Projects данными из кэша
                foreach ($cache['Projects'] as $key => $value) {
                    $model[$key] = $value;
                }

                $response = [
                    'renderAjax' => $this->renderAjax('create', [
                        'user' => $user,
                        'model' => $model,
                        'author' => $author
                    ]),
                    'cache' => $cache,
                ];
            }
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
     * @throws Exception
     */
    public function actionCreate(int $id)
    {
        $model = new Projects();
        $model->setUserId($id);
        $user = User::findOne($id);

        if ($model->load(Yii::$app->request->post())) {

            if(Yii::$app->request->isAjax) {

                //Проверка на совпадение по названию проекта у данного пользователя
                if ($model->validate(['project_name'])) {

                    if ($model->create()){

                        // Удаление кэша формы создания
                        $cachePath = $model::getCachePath($user);
                        $model->_cacheManager->deleteCache(mb_substr($cachePath, 0, -1));

                        $response =  [
                            'success' => true, 'count' => (int)Projects::find()->andWhere(['user_id' => $id])->count(),
                            'renderAjax' => $this->renderAjax('_index_ajax', [
                                'models' => Projects::findAll(['user_id' => $user->getId()]),
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }else{

                    //Проект с таким именем уже существует
                    $response =  ['project_already_exists' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
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
        $model = Projects::findOne($id);
        $workers = Authors::findAll(['project_id' => $id]);

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('update', [
                    'model' => $model,
                    'workers' => $workers
                ]),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $current_id
     * @param int $type_sort_id
     * @return array|bool
     */
    public function actionSortingModels(int $current_id, int $type_sort_id)
    {
        $sort = new ProjectSort();

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
     * @return \yii\console\Response|Response
     * @throws NotFoundHttpException
     */
    public function actionDownload(int $id)
    {
        /** @var $model PreFiles */
        $model = PreFiles::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $model->getProjectId()])
            ->one();

        $user = User::findOne(['id' => $project->getUserId()]);

        $path = UPLOAD.'/user-'.$user->getId().'/project-'.$project->getId().'/present_files/';
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
     * @throws Throwable
     */
    public function actionDeleteFile(int $id)
    {
        $model = PreFiles::findOne($id);
        $project = Projects::findOne(['id' => $model->getProjectId()]);
        $user = User::findOne(['id' => $project->getUserId()]);
        $path = UPLOAD.'/user-'.$user->getId().'/project-'.$project->getId().'/present_files/';

        if(unlink($path . $model->getServerFile()) && $model->delete()) {
            $models = PreFiles::findAll(['project_id' => $project->getId()]);

            if (Yii::$app->request->isAjax)
            {
                $response =  [
                    'success' => true,
                    'count_files' => count($models),
                    'project_id' => $project->getId(),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            return $this->redirect(['/projects/index', 'id' => $user->getId()]);
        }
        throw new NotFoundHttpException('Данный файл не найден');
    }


    /**
     * @return array
     */
    public function actionListTypeSort(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (isset($_POST['depdrop_parents'])) {

            $parents = $_POST['depdrop_parents'];

            if (!empty($parents) && $parents[0] != 0) {

                $cat_id = $parents[0];
                $out = ProjectSort::getListTypes($cat_id);
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    /**
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $user = User::findOne($model->getUserId());

        if ($model->load(Yii::$app->request->post())) {

            if(Yii::$app->request->isAjax) {

                //Проверка на совпадение по названию проекта у данного пользователя
                if ($model->validate(['project_name'])) {

                    if ($model->updateProject()){

                        $response =  [
                            'success' => true,
                            'renderAjax' => $this->renderAjax('_index_ajax', [
                                'models' => Projects::findAll(['user_id' => $user->getId()]),
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                } else{

                    //Проект с таким именем уже существует
                    $response =  ['project_already_exists' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * Включить разрешение на экспертизу
     *
     * @param int $id
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionEnableExpertise(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $project = Projects::findOne($id);
            if ($project->allowExpertise()) {

                $response = [
                    'renderAjax' => $this->renderAjax('_index_ajax', [
                        'models' => Projects::findAll(['user_id' => $project->getUserId()]),
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
     * @param int $id
     * @return array|bool
     */
    public function actionShowAllInformation(int $id)
    {
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('all-information', ['project' => $project]),
                'project' => $project,
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
     */
    public function actionShowRoadmap(int $id)
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        $roadmaps = [];

        /** @var $segments Segments[] */
        $segments = !$project->getDeletedAt() ?
            $project->segments :
            Segments::find(false)
                ->andWhere(['project_id' => $project->getId()])
                ->all();

        foreach ($segments as $i => $segment){
            $roadmaps[$i] = new Roadmap($segment->getId());
        }

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('roadmap', ['roadmaps' => $roadmaps]),
                'project' => $project,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionRoadmapMobile(int $id): string
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        $roadmaps = [];

        /** @var $segments Segments[] */
        $segments = !$project->getDeletedAt() ?
            $project->segments :
            Segments::find(false)
                ->andWhere(['project_id' => $project->getId()])
                ->all();

        foreach ($segments as $i => $segment){
            $roadmaps[$i] = new Roadmap($segment->getId());
        }

        return $this->render('roadmap-mobile', [
            'roadmaps' => $roadmaps]);
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionResults(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('results', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionRoadmaps(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('roadmaps', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionReports(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('reports', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionPresentations(int $id): string
    {
        $user = User::findOne($id);
        $projects = Projects::findAll(['user_id' => $id]);

        return $this->render('presentations', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }


    public function actionGetPresentation(int $id)
    {
        $project = Projects::findOne($id);

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('_presentation_ajax', ['project' => $project]),
                'project' => $project,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPresentationMobile(int $id): string
    {
        return $this->render('presentation-mobile', [
            'project' => $this->findModel($id, false)]);
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionResult(int $id)
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $segments Segments[] */
        $segments = !$project->getDeletedAt() ?
            $project->segments :
            Segments::find(false)
                ->andWhere(['project_id' => $id])
                ->all();

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('result', ['project' => $project, 'segments' => $segments]),
                'project' => $project,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionResultMobile(int $id): string
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $segments Segments[] */
        $segments = Segments::find(false)
            ->andWhere(['project_id' => $id])
            ->all();

        return $this->render('result-mobile', [
            'project' => $project,
            'segments' => $segments
        ]);
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionReport(int $id)
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $segments Segments[] */
        $segments = !$project->getDeletedAt() ?
            $project->segments :
            Segments::find(false)
                ->andWhere(['project_id' => $project->getId()])
                ->all();

        foreach ($segments as $s => $segment) {

            $segment->propertyContainer->addProperty('title', 'Сегмент ' . ($s+1));

            /** @var $problems Problems[] */
            $problems = !$project->getDeletedAt() ?
                $segment->problems :
                Problems::find(false)
                    ->andWhere(['segment_id' => $segment->getId()])
                    ->all();

            foreach ($problems as $p => $problem) {

                $problem->propertyContainer->addProperty('title', 'ГПС ' . ($s+1) . '.' . ($p+1));

                /** @var $gcps Gcps[] */
                $gcps = !$project->getDeletedAt() ?
                    $problem->gcps :
                    Gcps::find(false)
                        ->andWhere(['problem_id' => $problem->getId()])
                        ->all();

                foreach ($gcps as $g => $gcp) {

                    $gcp->propertyContainer->addProperty('title', 'ГЦП ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1));

                    /** @var $mvps Mvps[] */
                    $mvps = !$project->getDeletedAt() ?
                        $gcp->mvps :
                        Mvps::find(false)
                            ->andWhere(['gcp_id' => $gcp->getId()])
                            ->all();

                    foreach ($mvps as $m => $mvp) {

                        $mvp->propertyContainer->addProperty('title', 'MVP ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1) . '.' . ($m+1));
                    }
                }
            }
        }

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('report', ['segments' => $segments]),
                'project' => $project,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionReportMobile(int $id): string
    {
        /** @var $segments Segments[] */
        $segments = Segments::find(false)
            ->andWhere(['project_id' => $id])
            ->all();

        foreach ($segments as $s => $segment) {

            $segment->propertyContainer->addProperty('title', 'ГЦС ' . ($s+1));

            /** @var $problems Problems[] */
            $problems = Problems::find(false)
                ->andWhere(['segment_id' => $segment->getId()])
                ->all();

            foreach ($problems as $p => $problem) {

                $problem->propertyContainer->addProperty('title', 'ГПС ' . ($s+1) . '.' . ($p+1));

                /** @var $gcps Gcps[] */
                $gcps = Gcps::find(false)
                    ->andWhere(['problem_id' => $problem->getId()])
                    ->all();

                foreach ($gcps as $g => $gcp) {

                    $gcp->propertyContainer->addProperty('title', 'ГЦП ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1));

                    /** @var $mvps Mvps[] */
                    $mvps = Mvps::find(false)
                        ->andWhere(['gcp_id' => $gcp->getId()])
                        ->all();

                    foreach ($mvps as $m => $mvp) {

                        $mvp->propertyContainer->addProperty('title', 'MVP ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1) . '.' . ($m+1));
                    }
                }
            }
        }

        return $this->render('report-mobile', [
            'segments' => $segments]);
    }


    /**
     * @param int $id
     * @return string
     */
    public function actionResultExport(int $id): string
    {

        $segments = Segments::findAll(['project_id' => $id]);

        $businessModels = [];

        foreach ($segments as $segment) {

            if ($segment->problems) {

                foreach ($segment->problems as $problem) {

                    if ($problem->gcps) {

                        foreach ($problem->gcps as $gcp) {

                            if ($gcp->mvps) {

                                foreach ($gcp->mvps as $mvp) {

                                    if ($mvp->businessModel) {

                                        $businessModels[] = $mvp->businessModel;

                                    } else {

                                        $businessModel = new BusinessModel();
                                        $businessModel->setMvpId($mvp->getId());
                                        $businessModel->setGcpId($gcp->getId());
                                        $businessModel->setProblemId($problem->getId());
                                        $businessModel->setSegmentId($segment->getId());
                                        $businessModels[] = $businessModel;
                                    }
                                }
                            } else {

                                $businessModel = new BusinessModel();
                                $businessModel->setGcpId($gcp->getId());
                                $businessModel->setProblemId($problem->getId());
                                $businessModel->setSegmentId($segment->getId());
                                $businessModels[] = $businessModel;
                            }
                        }
                    } else {

                        $businessModel = new BusinessModel();
                        $businessModel->setProblemId($problem->getId());
                        $businessModel->setSegmentId($segment->getId());
                        $businessModels[] = $businessModel;
                    }
                }

            } else {

                $businessModel = new BusinessModel();
                $businessModel->setSegmentId($segment->getId());
                $businessModels[] = $businessModel;
            }
        }


        //Добавление нумерации
        $numberSegment = 0;
        foreach ($businessModels as $k => $businessModel) {

            if ($businessModel->segment->id !== $businessModels[$k - 1]->segment->id) {
                //Добавление номера сегмента
                $numberSegment++;
                $businessModel->segment->setName('Сегмент ' . $numberSegment . ': ' . $businessModel->segment->getName());

            } else {
                //Добавление номера сегмента
                $businessModel->segment->setName($businessModels[$k - 1]->segment->getName());
            }

            if ($businessModel->problem->title) {
                //Добавление номера ГПС
                $numberProblem = explode('ГПС ', $businessModel->problem->getTitle())[1];
                $businessModel->problem->setTitle('ГПС ' . $numberSegment . '.' . $numberProblem);

                if ($businessModel->gcp->title) {
                    //Добавление номера ГПС
                    $numberGcp = explode('ГЦП ', $businessModel->gcp->getTitle())[1];
                    $businessModel->gcp->setTitle('ГЦП ' . $numberSegment . '.' . $numberProblem . '.' . $numberGcp);

                    if ($businessModel->mvp->title) {
                        //Добавление номера MVP
                        $numberMvp = explode('MVP ', $businessModel->mvp->getTitle())[1];
                        $businessModel->mvp->setTitle('MVP ' . $numberSegment . '.' . $numberProblem . '.' . $numberGcp . '.' . $numberMvp);
                    }
                }
            }
        }

        // Отслеживаем совпадения в столбцах
        foreach ($businessModels as $k => $businessModel) {

            if ($businessModel->problem->gcps) {

                foreach ($businessModel->problem->gcps as $gcp) {
                    //Если id следующего ГЦП равно id предыдущего, то выполняем следующее
                    if ($businessModels[$k + 1]->gcp->id === $businessModel->gcp->id) {

                        $businessModels[$k + 1]->gcp->title = '';
                        $businessModels[$k + 1]->gcp->created_at = null;
                        $businessModels[$k + 1]->gcp->time_confirm = null;
                    }
                }
            }

            if ($businessModel->segment->problems) {

                foreach ($businessModel->segment->problems as $problem) {
                    //Если id следующего ГПС равно id предыдущего, то выполняем следующее
                    if ($businessModels[$k + 1]->problem->id === $businessModel->problem->id) {

                        $businessModels[$k + 1]->problem->title = '';
                        $businessModels[$k + 1]->problem->created_at = null;
                        $businessModels[$k + 1]->problem->time_confirm = null;
                    }
                }
            }
        }


        $project = Projects::findOne($id);
        $project_filename = str_replace(' ', '_', $project->getProjectName());
        $dataProvider = new ArrayDataProvider(['allModels' => $businessModels, 'pagination' => false, 'sort' => false]);


        return $this->render('result-export',[
            'dataProvider' => $dataProvider,
            'project' => $project,
            'project_filename' => $project_filename,
        ]);

    }


    /**
     * @param $id
     * @return string
     */
    /*public function actionReportTest ($id) {

        $segments = Segments::find()->andWhere(['project_id' => $id])->with(['confirm', 'problems'])->all();

        $statModels = [];

        foreach ($segments as $s => $segment) {

            if (empty($segment->problems)) {

                $newProblem = new Problems();
                $newProblem->segment_id = $segment->id;
                $newProblem->project_id = $id;
                $newProblem->description = 'У данного сегмента отсутствуют дальнейшие этапы';
                $statModels[] = $newProblem;
            }

            if ($segment->confirm && $segment->problems) {

                $problems = Problems::find()->andWhere(['segment_id' => $segment->id])->with(['gcps'])->all();

                foreach ($problems as $p => $problem) {

                    $problem->description = 'ГПС ' . ($s+1) . '.' . ($p+1) . ': ' . $problem->description;

                    $statModels[] = $problem;

                    if ($segment->confirm && $segment->problems && $problem->gcps) {

                        $gcps = Gcps::find()->andWhere(['problem_id' => $problem->id])->with(['mvps'])->all();

                        foreach ($gcps as $g => $gcp) {

                            $gcp->description = 'ГЦП ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1) . ': ' . $gcp->description;

                            $statModels[] = $gcp;

                            if ($segment->confirm && $segment->problems && $problem->gcps && $gcp->mvps){

                                $mvps = Mvps::find()->andWhere(['gcp_id' => $gcp->id])->with(['businessModel'])->all();

                                foreach ($mvps as $m => $mvp) {

                                    $mvp->description = 'ГMVP ' . ($s+1) . '.' . ($p+1) . '.' . ($g+1) . '.' . ($m+1) . ': ' . $mvp->description;

                                    $statModels[] = $mvp;
                                }
                            }
                        }
                    }
                }
            }
        }


        $dataProvider = new ArrayDataProvider([
            'allModels' => $statModels,
            //'pagination' => [
                //'pageSize' => 100,
            //],
            'pagination' => false,
            'sort' => false,
        ]);

        $project = Projects::findOne($id);
        $project_filename = str_replace(' ', '_', $project->project_name);

        return $this->render('report-test', [
                'dataProvider' => $dataProvider,
                'project' => $project,
                'project_filename' => $project_filename,
            ]
        );
    }*/


    /**
     * @param int $id
     * @return mixed
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws InvalidConfigException
     */
    public function actionMpdfProject(int $id) {

        $model = Projects::findOne($id);

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('mpdf_project', ['project' => $model]);

        $destination = Pdf::DEST_BROWSER;
        //$destination = Pdf::DEST_DOWNLOAD;

        $filename = 'Презентация проекта «'.$model->getProjectName() .'».pdf';

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
                'SetTitle' => [$model->getProjectName()],
                'SetHeader' => ['<div style="color: #3c3c3c;">Проект «'.$model->getProjectName().'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
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
    public function actionMpdfBusinessModel(int $id) {

        $model = BusinessModel::findOne($id);

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('/business-model/viewpdf', ['model' => $model]);

        $destination = Pdf::DEST_BROWSER;
        //$destination = Pdf::DEST_DOWNLOAD;

        $filename = 'business-model-'. $model->getId() .'.pdf';

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
            'cssInline' => '.business-model-view-export {color: #3c3c3c;};',
            'marginFooter' => 5,
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' => ['Бизнес-модель PDF'],
                'SetHeader' => ['<div style="color: #3c3c3c;">Бизнес-модель для проекта «'.$model->project->getProjectName().'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
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
     * @param int|string $id
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteAuthor($id): void
    {
        $model = Authors::findOne($id);

        if ($model){
            $project = Projects::findOne(['id' => $model->getProjectId()]);
            $project->updated_at = time();
            $model->delete();
        }
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
            return $this->redirect(['index', 'id' => $model->getUserId()]);
        }

        PatternHttpException::noData();
    }


    /**
     * Finds the Projects model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return Projects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): Projects
    {
        if (!$isOnlyNotDelete) {
            $model = Projects::find(false)
                ->andWhere(['id' => $id])
                ->one();
        } else {
            $model = Projects::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
