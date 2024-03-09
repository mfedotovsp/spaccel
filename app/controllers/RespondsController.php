<?php


namespace app\controllers;

use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\CacheForm;
use app\models\forms\CreateRespondGcpForm;
use app\models\forms\CreateRespondMvpForm;
use app\models\forms\CreateRespondProblemForm;
use app\models\forms\CreateRespondSegmentForm;
use app\models\forms\FormCreateRespondent;
use app\models\forms\FormUpdateConfirmGcp;
use app\models\forms\FormUpdateConfirmMvp;
use app\models\forms\FormUpdateConfirmProblem;
use app\models\forms\FormUpdateConfirmSegment;
use app\models\forms\UpdateRespondProblemForm;
use app\models\forms\UpdateRespondGcpForm;
use app\models\forms\UpdateRespondMvpForm;
use app\models\forms\UpdateRespondSegmentForm;
use app\models\PatternHttpException;
use app\models\RespondsGcp;
use app\models\RespondsMvp;
use app\models\RespondsProblem;
use app\models\RespondsSegment;
use app\models\StageConfirm;
use app\models\User;
use Throwable;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * Контроллер с методами создания, редактирования и получения информации
 * о респондентах, которые проходят интервью при подтверждении гипотезы
 *
 * Class RespondsController
 * @package app\controllers
 */
class RespondsController extends AppUserPartController
{

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {

        if (in_array($action->id, ['update', 'delete'])){

            $model = self::findModel(Yii::$app->request->get('stage'), (int)Yii::$app->request->get('id'));
            $confirm = $model->confirm;
            $hypothesis = $confirm->hypothesis;
            $project = $hypothesis->project;

            if ($project->getUserId() === Yii::$app->user->getId()){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            if ($model->getContractorId() === Yii::$app->user->getId()) {
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif ($action->id === 'create'){

            $confirm = self::getConfirm(Yii::$app->request->get('stage'), (int)Yii::$app->request->get('id'));
            $hypothesis = $confirm->hypothesis;
            $project = $hypothesis->project;

            if ($project->getUserId() === Yii::$app->user->getId()){
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
     * Проверка доступа
     * к заполнению интервью
     *
     * @param int $stage
     * @param int $id
     * @return array|bool
     */
    public function actionDataAvailability(int $stage, int $id)
    {
        if(Yii::$app->request->isAjax) {

            if (User::isUserSimple(Yii::$app->user->identity['username']) || User::isUserContractor(Yii::$app->user->identity['username'])) {
                $response =  ['success' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

//            TODO: Скорее всего эти ограничения более не акутуальны
//            $count_models = 0; // Кол-во респондентов подтверждения
//            $count_exist_data_respond = 0; // Кол-во респондентов, у кот-х заполнены данные
//            $count_exist_data_descInterview = 0; // Кол-во респондентов, у кот-х существует интервью
//
//            if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
//
//                $count_models = RespondsSegment::find()->andWhere(['confirm_id' => $id])->count();
//
//                $count_exist_data_respond = RespondsSegment::find()->andWhere(['confirm_id' => $id])->andWhere(['not', ['info_respond' => '']])
//                    ->andWhere(['not', ['date_plan' => null]])->andWhere(['not', ['place_interview' => '']])->count();
//
//                $count_exist_data_descInterview = RespondsSegment::find()->with('interview')
//                    ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
//                    ->andWhere(['confirm_id' => $id])->andWhere(['not', ['interview_confirm_segment.id' => null]])->count();
//
//            } elseif ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
//
//                $count_models = RespondsProblem::find()->andWhere(['confirm_id' => $id])->count();
//
//                $count_exist_data_respond = RespondsProblem::find()->andWhere(['confirm_id' => $id])->andWhere(['not', ['info_respond' => '']])
//                    ->andWhere(['not', ['date_plan' => null]])->andWhere(['not', ['place_interview' => '']])->count();
//
//                $count_exist_data_descInterview = RespondsProblem::find()->with('interview')
//                    ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
//                    ->andWhere(['confirm_id' => $id])->andWhere(['not', ['interview_confirm_problem.id' => null]])->count();
//
//            } elseif ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
//
//                $count_models = RespondsGcp::find()->andWhere(['confirm_id' => $id])->count();
//
//                $count_exist_data_respond = RespondsGcp::find()->andWhere(['confirm_id' => $id])->andWhere(['not', ['info_respond' => '']])
//                    ->andWhere(['not', ['date_plan' => null]])->andWhere(['not', ['place_interview' => '']])->count();
//
//                $count_exist_data_descInterview = RespondsGcp::find()->with('interview')
//                    ->leftJoin('interview_confirm_gcp', '`interview_confirm_gcp`.`respond_id` = `responds_gcp`.`id`')
//                    ->andWhere(['confirm_id' => $id])->andWhere(['not', ['interview_confirm_gcp.id' => null]])->count();
//
//            } elseif ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
//
//                $count_models = RespondsMvp::find()->andWhere(['confirm_id' => $id])->count();
//
//                $count_exist_data_respond = RespondsMvp::find()->andWhere(['confirm_id' => $id])->andWhere(['not', ['info_respond' => '']])
//                    ->andWhere(['not', ['date_plan' => null]])->andWhere(['not', ['place_interview' => '']])->count();
//
//                $count_exist_data_descInterview = RespondsMvp::find()->with('interview')
//                    ->leftJoin('interview_confirm_mvp', '`interview_confirm_mvp`.`respond_id` = `responds_mvp`.`id`')
//                    ->andWhere(['confirm_id' => $id])->andWhere(['not', ['interview_confirm_mvp.id' => null]])->count();
//            }
//
//            if (($count_exist_data_respond === $count_models) || ($count_exist_data_descInterview > 0)) {
//
//                $response =  ['success' => true];
//                Yii::$app->response->format = Response::FORMAT_JSON;
//                Yii::$app->response->data = $response;
//                return $response;
//
//            }

            $response = ['error' => true];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Сохранение кэша из формы
     * создания респондента
     *
     * @param int $stage
     * @param int $id
     */
    public function actionSaveCacheCreationForm(int $stage, int $id): void
    {
        /**
         * @var FormCreateRespondent $class
         */
        $confirm = self::getConfirm($stage, $id);
        $class = self::getClassCreateModel($stage);
        $cachePath = $class::getCachePath($confirm);
        $cacheName = 'formCreateRespondCache';

        if(Yii::$app->request->isAjax) {

            $cache = new CacheForm();
            $cache->setCache($cachePath, $cacheName);
        }
    }


    /**
     * @param int $stage
     * @param int $id
     * @param bool $isMobile
     * @return array|bool
     */
    public function actionGetDataCreateForm(int $stage, int $id, bool $isMobile = false)
    {
        $confirm = self::getConfirm($stage, $id);
        $model = self::getCreateModel($stage, $confirm);

        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('create', ['confirm' => $confirm, 'model' => $model, 'isMobile' => $isMobile])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @return array|bool
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $stage, int $id)
    {
        $confirm = self::getConfirm($stage, $id);
        $newRespond = $this::getCreateModel($stage, $confirm);
        $newRespond->setConfirmId($id);

        if ($newRespond->load(Yii::$app->request->post())) {

            if(Yii::$app->request->isAjax) {

                if ($confirm->checkingLimitCountRespond()) {

                    if ($newRespond->validate(['name'])) {

                        if ($newRespond = $newRespond->create()) {

                            // Обновление данных подтверждения
                            $confirm->setCountRespond(++$confirm->count_respond);
                            $confirm->save();

                            $responds = $confirm->responds;
                            $page = floor((count($responds) - 1) / 10) + 1;
                            $response = array();

                            if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
                                $segment = $confirm->segment;
                                $project = $segment->project;
                                $response =  [
                                    'newRespond' => $newRespond,
                                    'responds' => $responds,
                                    'page' => $page,
                                    'confirm_id' => $id,
                                    'ajax_data_confirm' => $this->renderAjax('/confirm-segment/ajax_data_confirm', ['model' => ConfirmSegment::findOne($id), 'formUpdateConfirmSegment' => new FormUpdateConfirmSegment($id), 'project' => $project]),
                                ];

                            } elseif ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
                                $problem = $confirm->problem;
                                $response =  [
                                    'newRespond' => $newRespond,
                                    'responds' => $responds,
                                    'page' => $page,
                                    'confirm_id' => $id,
                                    'ajax_data_confirm' => $this->renderAjax('/confirm-problem/ajax_data_confirm', ['model' => ConfirmProblem::findOne($id), 'problem' => $problem, 'formUpdateConfirmProblem' => new FormUpdateConfirmProblem($id)]),
                                ];

                            } elseif ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
                                $gcp = $confirm->gcp;
                                $response =  [
                                    'newRespond' => $newRespond,
                                    'responds' => $responds,
                                    'page' => $page,
                                    'confirm_id' => $id,
                                    'ajax_data_confirm' => $this->renderAjax('/confirm-gcp/ajax_data_confirm', ['model' => ConfirmGcp::findOne($id), 'gcp' => $gcp, 'formUpdateConfirmGcp' => new FormUpdateConfirmGcp($id)]),
                                ];

                            } elseif ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
                                $mvp = $confirm->mvp;
                                $response =  [
                                    'newRespond' => $newRespond,
                                    'responds' => $responds,
                                    'page' => $page,
                                    'confirm_id' => $id,
                                    'ajax_data_confirm' => $this->renderAjax('/confirm-mvp/ajax_data_confirm', ['model' => ConfirmMvp::findOne($id), 'mvp' => $mvp, 'formUpdateConfirmMvp' => new FormUpdateConfirmMvp($id)]),
                                ];
                            }

                            Yii::$app->response->format = Response::FORMAT_JSON;
                            Yii::$app->response->data = $response;
                            return $response;
                        }
                    } else {
                        $response = ['error' => true];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                } else {
                    $response = ['limit_count_respond' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
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
        $confirm = $model->findConfirm($isOnlyNotDelete);

        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('update', ['model' => $model, 'confirm' => $confirm, 'isOnlyNotDelete' => $isOnlyNotDelete])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $stage, int $id)
    {
        $model = self::getUpdateModel($stage, $id);
        $confirm = $model->findConfirm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            if ($model->validate(['name'])){

                if ($model->update()){

                    $response = ['confirm_id' => $confirm->getId()];
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
     * @return RespondsProblem|RespondsGcp|RespondsMvp|RespondsSegment|bool|null
     * @throws NotFoundHttpException
     */
    public function actionGetDataModel(int $stage, int $id)
    {
        $model = self::findModel($stage, $id);

        if(Yii::$app->request->isAjax) {

            $response = $model;
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @param string $search
     * @param bool $isMobile
     * @param bool $isOnlyNotDelete
     * @param bool $isModuleContractor
     * @return array|bool
     */
    public function actionGetQueryResponds(int $stage, int $id, string $search = '', bool $isMobile = false, bool $isOnlyNotDelete = true, bool $isModuleContractor = false)
    {
        $task = null;
        if ($isModuleContractor) {
            $task = ContractorTasks::findOne($id);
            $id = $task->getHypothesisId();
        }

        $confirm = self::getConfirm($stage, $id, $isOnlyNotDelete);
        $queryResponds = self::getQueryModels($stage, $id, $search, $isOnlyNotDelete);
        if ($task && $task->activity->getTitle() === 'Полевая работа') {
            $queryResponds->andWhere(['task_id' => $task->getId()]);
        }

        $currentTaskId = $task ? $task->getId() : null;
        $responds = $queryResponds->all();

        if(Yii::$app->request->isAjax) {

            $response = array();

            if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
                $response = ['ajax_data_responds' => $this->renderAjax('respondsForConfirmSegment', [
                    'confirm' => $confirm, 'responds' => $responds, 'isMobile' => $isMobile, 'isOnlyNotDelete' => $isOnlyNotDelete, 'isModuleContractor' => $isModuleContractor, 'currentTaskId' => $currentTaskId])];

            } elseif ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
                $response = ['ajax_data_responds' => $this->renderAjax('respondsForConfirmProblem', [
                    'confirm' => $confirm, 'responds' => $responds, 'isMobile' => $isMobile, 'isOnlyNotDelete' => $isOnlyNotDelete, 'isModuleContractor' => $isModuleContractor, 'currentTaskId' => $currentTaskId])];

            } elseif ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
                $response = ['ajax_data_responds' => $this->renderAjax('respondsForConfirmGcp', [
                    'confirm' => $confirm, 'responds' => $responds, 'isMobile' => $isMobile, 'isOnlyNotDelete' => $isOnlyNotDelete, 'isModuleContractor' => $isModuleContractor, 'currentTaskId' => $currentTaskId])];

            } elseif ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
                $response = ['ajax_data_responds' => $this->renderAjax('respondsForConfirmMvp', [
                    'confirm' => $confirm, 'responds' => $responds, 'isMobile' => $isMobile, 'isOnlyNotDelete' => $isOnlyNotDelete, 'isModuleContractor' => $isModuleContractor, 'currentTaskId' => $currentTaskId])];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete (int $stage, int $id)
    {
        $model = self::findModel($stage, $id);
        $confirm = $model->confirm;
        $hypothesis = $confirm->hypothesis;
        $project = $hypothesis->project;

        if (Yii::$app->request->isAjax){

            if (User::isUserSimple(Yii::$app->user->identity['username'])) {

                if ($confirm->getCountRespond() === 1){

                    $response = ['zero_value_responds' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
                elseif ($confirm->getCountRespond() === $confirm->getCountPositive()){

                    $response = ['number_less_than_allowed' => true];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
                else {

                    if ($model->delete()) {

                        //Обновление данных подтверждения
                        $confirm->setCountRespond(--$confirm->count_respond);
                        $confirm->save();

                        $response = array();

                        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
                            $response = [
                                'success' => true, 'confirm_id' => $model->getConfirmId(),
                                'ajax_data_confirm' => $this->renderAjax('/confirm-segment/ajax_data_confirm', [
                                    'model' => ConfirmSegment::findOne($model->getConfirmId()), 'project' => $project,
                                    'formUpdateConfirmSegment' => new FormUpdateConfirmSegment($model->getConfirmId())]),
                            ];

                        } elseif ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
                            $response = [
                                'success' => true, 'confirm_id' => $model->getConfirmId(),
                                'ajax_data_confirm' => $this->renderAjax('/confirm-problem/ajax_data_confirm', [
                                    'model' => ConfirmProblem::findOne($model->getConfirmId()), 'problem' => $hypothesis,
                                    'formUpdateConfirmProblem' => new FormUpdateConfirmProblem($model->getConfirmId())]),
                            ];

                        } elseif ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
                            $response = [
                                'success' => true, 'confirm_id' => $model->getConfirmId(),
                                'ajax_data_confirm' => $this->renderAjax('/confirm-gcp/ajax_data_confirm', [
                                    'model' => ConfirmGcp::findOne($model->getConfirmId()), 'gcp' => $hypothesis,
                                    'formUpdateConfirmGcp' => new FormUpdateConfirmGcp($model->getConfirmId())]),
                            ];

                        } elseif ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
                            $response = [
                                'success' => true, 'confirm_id' => $model->getConfirmId(),
                                'ajax_data_confirm' => $this->renderAjax('/confirm-mvp/ajax_data_confirm', [
                                    'model' => ConfirmMvp::findOne($model->getConfirmId()), 'mvp' => $hypothesis,
                                    'formUpdateConfirmMvp' => new FormUpdateConfirmMvp($model->getConfirmId())]),
                            ];
                        }

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                    return false;
                }
            }

            if (User::isUserContractor(Yii::$app->user->identity['username']) && $model->clearData()) {
                $response = array();

                if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
                    $response = [
                        'success' => true, 'confirm_id' => $model->getConfirmId(),
                        'ajax_data_confirm' => $this->renderAjax('/confirm-segment/ajax_data_confirm', [
                            'model' => ConfirmSegment::findOne($model->getConfirmId()), 'project' => $project,
                            'formUpdateConfirmSegment' => new FormUpdateConfirmSegment($model->getConfirmId())]),
                    ];

                } elseif ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
                    $response = [
                        'success' => true, 'confirm_id' => $model->getConfirmId(),
                        'ajax_data_confirm' => $this->renderAjax('/confirm-problem/ajax_data_confirm', [
                            'model' => ConfirmProblem::findOne($model->getConfirmId()), 'problem' => $hypothesis,
                            'formUpdateConfirmProblem' => new FormUpdateConfirmProblem($model->getConfirmId())]),
                    ];

                } elseif ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
                    $response = [
                        'success' => true, 'confirm_id' => $model->getConfirmId(),
                        'ajax_data_confirm' => $this->renderAjax('/confirm-gcp/ajax_data_confirm', [
                            'model' => ConfirmGcp::findOne($model->getConfirmId()), 'gcp' => $hypothesis,
                            'formUpdateConfirmGcp' => new FormUpdateConfirmGcp($model->getConfirmId())]),
                    ];

                } elseif ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
                    $response = [
                        'success' => true, 'confirm_id' => $model->getConfirmId(),
                        'ajax_data_confirm' => $this->renderAjax('/confirm-mvp/ajax_data_confirm', [
                            'model' => ConfirmMvp::findOne($model->getConfirmId()), 'mvp' => $hypothesis,
                            'formUpdateConfirmMvp' => new FormUpdateConfirmMvp($model->getConfirmId())]),
                    ];
                }

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
     * @param string $search
     * @param bool $isOnlyNotDelete
     * @return bool|ActiveQuery
     */
    private static function getQueryModels(int $stage, int $id, string $search, bool $isOnlyNotDelete = true)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return $isOnlyNotDelete ?
                RespondsSegment::find()
                ->andWhere(['confirm_id' => $id])
                ->andWhere(['like', 'name', $search]) :
                RespondsSegment::find(false)
                    ->andWhere(['confirm_id' => $id])
                    ->andWhere(['like', 'name', $search]);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return $isOnlyNotDelete ?
                RespondsProblem::find()
                ->andWhere(['confirm_id' => $id])
                ->andWhere(['like', 'name', $search]) :
                RespondsProblem::find(false)
                    ->andWhere(['confirm_id' => $id])
                    ->andWhere(['like', 'name', $search]);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return $isOnlyNotDelete ?
                RespondsGcp::find()
                ->andWhere(['confirm_id' => $id])
                ->andWhere(['like', 'name', $search]) :
                RespondsGcp::find(false)
                    ->andWhere(['confirm_id' => $id])
                    ->andWhere(['like', 'name', $search]);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return $isOnlyNotDelete ?
                RespondsMvp::find()
                ->andWhere(['confirm_id' => $id])
                ->andWhere(['like', 'name', $search]) :
                RespondsMvp::find(false)
                    ->andWhere(['confirm_id' => $id])
                    ->andWhere(['like', 'name', $search]);
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @return RespondsProblem|RespondsGcp|RespondsMvp|RespondsSegment|null
     * @throws NotFoundHttpException
     */
    private static function findModel(int $stage, int $id)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return RespondsSegment::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return RespondsProblem::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return RespondsGcp::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return RespondsMvp::findOne($id);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * @param int $stage
     * @return bool|string
     */
    private static function getClassModel(int $stage)
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
     * @param $confirm
     * @return CreateRespondSegmentForm|CreateRespondProblemForm|CreateRespondGcpForm|CreateRespondMvpForm|bool
     */
    private static function getCreateModel(int $stage, $confirm)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return new CreateRespondSegmentForm($confirm);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new CreateRespondProblemForm($confirm);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return new CreateRespondGcpForm($confirm);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return new CreateRespondMvpForm($confirm);
        }
        return false;
    }


    /**
     * @param int $stage
     * @return bool|string
     */
    private static function getClassCreateModel(int $stage)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return CreateRespondSegmentForm::class;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return CreateRespondProblemForm::class;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return CreateRespondGcpForm::class;
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return CreateRespondMvpForm::class;
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

}