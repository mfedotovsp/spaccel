<?php

namespace app\modules\contractor\controllers;

use app\models\ContractorTasks;
use app\models\forms\FormCreateSegment;
use app\models\forms\FormFilterRequirement;
use app\models\forms\FormUpdateSegment;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\RequirementWishList;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use kartik\mpdf\Pdf;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SegmentsController extends AppContractorController
{
    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'task') {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('id'));
            if (!$task || $task->getType() !== StageExpertise::SEGMENT) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserSimple($currentUser->getUsername()) && $task->project->getUserId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }else{
            return parent::beforeAction($action);
        }
    }

    /**
     * @param int $id
     * @return string
     */
    public function actionTask(int $id): string
    {
        $task = ContractorTasks::findOne($id);

        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $task->getProjectId()])
            ->one();

        $models = Segments::findAll([
            'project_id' => $project->getId(),
            'contractor_id' => $task->getContractorId(),
            'task_id' => $task->getId()
        ]);

        $existTrashList = Segments::find(false)
            ->andWhere(['project_id' => $project->getId()])
            ->andWhere(['contractor_id' => $task->getContractorId()])
            ->andWhere(['task_id' => $task->getId()])
            ->andWhere(['not', ['deleted_at' => null]])
            ->exists();

        $trashList = Segments::find(false)
            ->andWhere(['project_id' => $project->getId()])
            ->andWhere(['contractor_id' => $task->getContractorId()])
            ->andWhere(['task_id' => $task->getId()])
            ->andWhere(['not', ['deleted_at' => null]])
            ->all();

        return $this->render('task', [
            'task' => $task,
            'project' => $project,
            'models' => $models,
            'existTrashList' => $existTrashList,
            'trashList' => $trashList,
            'formTaskComplete' => new FormTaskComplete(),
        ]);
    }

    /**
     * @param int $id
     * @return array|false
     */
    public function actionList(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($id);
            $project = $task->hypothesis;

            $response = [
                'renderAjax' => $this->renderAjax('_index_ajax', [
                    'task' => $task,
                    'formTaskComplete' => new FormTaskComplete(),
                    'models' => Segments::findAll([
                        'project_id' => $project->getId(),
                        'contractor_id' => $task->getContractorId(),
                        'task_id' => $task->getId()
                    ])
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

            $task = ContractorTasks::findOne($id);
            $project = $task->hypothesis;

            $queryModels = Segments::find(false)
                ->andWhere(['project_id' => $project->getId()])
                ->andWhere(['contractor_id' => $task->getContractorId()])
                ->andWhere(['task_id' => $task->getId()])
                ->andWhere(['not', ['deleted_at' => null]]);

            $response = [
                'countItems' => $queryModels->count(),
                'renderAjax' => $this->renderAjax('_trash_ajax', [
                    'task' => $task,
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
     * @param bool|null $useWishList
     * @param int|null $requirementId
     * @return array|bool
     */
    public function actionGetHypothesisToCreate(int $id, bool $useWishList = null, int $requirementId = null)
    {
        $task = ContractorTasks::findOne($id);
        $project = $task->project;
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
                    'task' => $task
                ]),
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
        $task = ContractorTasks::findOne($id);
        $project = $task->project;
        $model = new FormCreateSegment($project);
        $model->setContractorId(Yii::$app->user->getId());
        $model->setTaskId($task->getId());
        $model->setProjectId($project->getId());

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            if ($model->checkFillingFields()) {

                if ($model->validate(['name'])) {

                    if ($model->create()) {

                        $response =  [
                            'success' => true,
                            'count' => Segments::find(false)
                                ->andWhere(['project_id' => $project->getId()])
                                ->andWhere(['contractor_id' => $task->getContractorId()])
                                ->andWhere(['task_id' => $task->getId()])
                                ->count(),
                            'renderAjax' => $this->renderAjax('_index_ajax', [
                                'task' => $task,
                                'formTaskComplete' => new FormTaskComplete(),
                                'models' => Segments::findAll([
                                    'project_id' => $project->getId(),
                                    'contractor_id' => $task->getContractorId(),
                                    'task_id' => $task->getId()
                                ])
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
     * @param int $taskId
     * @param int $page
     * @return array|false
     */
    public function actionGetListRequirements(int $taskId, int $page = 1)
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
                    'taskId' => $taskId,
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
                                'task' => ContractorTasks::findOne($segment->getTaskId()),
                                'formTaskComplete' => new FormTaskComplete(),
                                'models' => Segments::findAll([
                                    'project_id' => $project->getId(),
                                    'contractor_id' => $segment->getContractorId(),
                                    'task_id' => $segment->getTaskId()
                                ])
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
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Throwable
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
     * @throws \Throwable
     */
    public function actionRecovery(int $id)
    {
        $model = $this->findModel($id, false);

        if($model->recoveryStage()) {
            return $this->redirect(['task', 'id' => $model->getTaskId()]);
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