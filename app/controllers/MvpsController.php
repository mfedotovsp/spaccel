<?php

namespace app\controllers;

use app\models\ClientSettings;
use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\ConfirmGcp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\CacheForm;
use app\models\forms\FormCreateMvp;
use app\models\Gcps;
use app\models\PatternHttpException;
use app\models\Problems;
use app\models\Projects;
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
use app\models\Mvps;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер с методами для создания,
 * редактирования и получения информации по MVP
 *
 * Class MvpsController
 * @package app\controllers
 */
class MvpsController extends AppUserPartController
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

            $model = Mvps::findOne((int)Yii::$app->request->get('id'));
            $project = Projects::findOne($model->getProjectId());

            if ($project->getUserId() === $currentUser->getId()){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif ($action->id === 'create'){

            $confirmGcp = ConfirmGcp::findOne((int)Yii::$app->request->get('id'));
            $gcp = Gcps::findOne($confirmGcp->getGcpId());
            $project = Projects::findOne($gcp->getProjectId());

            if ($project->getUserId() === $currentUser->getId()){
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif (in_array($action->id, ['index', 'mpdf-table-mvps'])){

            /** @var $confirmGcp ConfirmGcp */
            $confirmGcp = ConfirmGcp::find(false)
                ->andWhere(['id' => (int)Yii::$app->request->get('id')])
                ->one();

            if (!$confirmGcp) {
                PatternHttpException::noData();
            }

            /** @var $gcp Gcps */
            $gcp = Gcps::find(false)
                ->andWhere(['id' => $confirmGcp->getGcpId()])
                ->one();

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $gcp->getProjectId()])
                ->one();

            if (($project->getUserId() === $currentUser->getId())){
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
     * @return string|Response
     */
    public function actionIndex(int $id)
    {
        $models = Mvps::findAll(['basic_confirm_id' => $id]);
        $countModels = Mvps::find(false)
            ->andWhere(['basic_confirm_id' => $id])
            ->count();

        if ((int)$countModels === 0) {
            return $this->redirect(['instruction', 'id' => $id]);
        }

        /** @var $confirmGcp ConfirmGcp */
        $confirmGcp = ConfirmGcp::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $gcp Gcps */
        $gcp = Gcps::find(false)
            ->andWhere(['id' => $confirmGcp->getGcpId()])
            ->one();

        /** @var $confirmProblem ConfirmProblem */
        $confirmProblem = ConfirmProblem::find(false)
            ->andWhere(['id' => $gcp->getConfirmProblemId()])
            ->one();

        /** @var $problem Problems */
        $problem = Problems::find(false)
            ->andWhere(['id' => $confirmProblem->getProblemId()])
            ->one();

        /** @var $confirmSegment ConfirmSegment */
        $confirmSegment = ConfirmSegment::find(false)
            ->andWhere(['id' => $problem->getConfirmSegmentId()])
            ->one();

        /** @var $segment Segments */
        $segment = Segments::find(false)
            ->andWhere(['id' => $confirmSegment->getSegmentId()])
            ->one();

        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $segment->getProjectId()])
            ->one();

        $existTrashList = Mvps::find(false)
            ->andWhere(['basic_confirm_id' => $id])
            ->andWhere(['not', ['deleted_at' => null]])
            ->exists();

        $trashList = Mvps::find(false)
            ->andWhere(['basic_confirm_id' => $id])
            ->andWhere(['not', ['deleted_at' => null]])
            ->all();

        return $this->render('index', [
            'models' => $models,
            'confirmGcp' => $confirmGcp,
            'gcp' => $gcp,
            'confirmProblem' => $confirmProblem,
            'problem' => $problem,
            'confirmSegment' => $confirmSegment,
            'segment' => $segment,
            'project' => $project,
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
                    'models' => Mvps::findAll(['basic_confirm_id' => $id])
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

            $queryModels = Mvps::find(false)
                ->andWhere(['basic_confirm_id' => $id])
                ->andWhere(['not', ['deleted_at' => null]]);

            $response = [
                'countItems' => $queryModels->count(),
                'renderAjax' => $this->renderAjax('_trash_ajax', [
                    'basicConfirmId' => $id,
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
     * @return string|Response
     */
    public function actionInstruction (int $id)
    {
        $countModels = Mvps::find(false)
            ->andWhere(['basic_confirm_id' => $id])
            ->count();

        if ((int)$countModels > 0) {
            return $this->redirect(['index', 'id' => $id]);
        }

        return $this->render('index_first', [
            'confirmGcp' => ConfirmGcp::findOne($id),
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
     * @param int|null $taskId
     * @return bool
     * @throws Throwable
     */
    public function actionSaveCacheCreationForm(int $id, int $taskId = null): bool
    {
        if(Yii::$app->request->isAjax) {

            try {
                if (!$taskId) {
                    $confirmGcp = ConfirmGcp::findOne($id);
                } else {
                    $task = ContractorTasks::findOne($taskId);
                    $confirmGcp = ConfirmGcp::findOne($task->getHypothesisId());
                    if ($task->getStatus() === ContractorTasks::TASK_STATUS_NEW) {
                        $task->changeStatus(ContractorTasks::TASK_STATUS_PROCESS);
                    }
                }

            } catch (\Exception $exception) {
                return false;
            }

            $cachePath = FormCreateMvp::getCachePath($confirmGcp->hypothesis);
            $cacheName = 'formCreateHypothesisCache';

            $cache = new CacheForm();
            $cache->setCache($cachePath, $cacheName);
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
        if (Yii::$app->request->isAjax) {

            $confirmGcp = ConfirmGcp::findOne($id);
            $model = new FormCreateMvp($confirmGcp->hypothesis);
            $model->setBasicConfirmId($id);

            if ($model->load(Yii::$app->request->post())) {

                if ($model->create()) {

                    $response = [
                        'count' => Mvps::find(false)->andWhere(['basic_confirm_id' => $id])->count(),
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                        'models' => Mvps::findAll(['basic_confirm_id' => $id])
                    ])];
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
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = $this->findModel($id);
            $confirmGcp = ConfirmGcp::findOne($model->getConfirmGcpId());

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()){

                    $response = [
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                            'models' => Mvps::findAll(['basic_confirm_id' => $confirmGcp->getId()]),
                        ]),
                    ];
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
        if (Yii::$app->request->isAjax) {

            $mvp = Mvps::findOne($id);
            $confirmGcp = ConfirmGcp::findOne($mvp->getConfirmGcpId());

            if ($mvp->allowExpertise()) {

                $response = [
                    'renderAjax' => $this->renderAjax('_index_ajax', [
                        'models' => Mvps::findAll(['basic_confirm_id' => $confirmGcp->getId()]),
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
     * @throws NotFoundHttpException
     */
    public function actionGetHypothesisToUpdate (int $id)
    {
        $model = $this->findModel($id);

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
     * @return mixed
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws InvalidConfigException
     */
    public function actionMpdfTableMvps(int $id)
    {
        /** @var $confirm_gcp ConfirmGcp */
        $confirm_gcp = ConfirmGcp::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$confirm_gcp->getDeletedAt()) {
            $gcp = $confirm_gcp->gcp;
            $models = $confirm_gcp->mvps;
        } else {
            /** @var $gcp Gcps */
            $gcp = Gcps::find(false)
                ->andWhere(['id' => $confirm_gcp->getGcpId()])
                ->one();

            /** @var $models Mvps[]*/
            $models = Mvps::find(false)
                ->andWhere(['basic_confirm_id' => $confirm_gcp->getId()])
                ->all();
        }

        $gcp_description = mb_substr($gcp->getDescription(), 0, 100).'...';
        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('mpdf_table_mvps', ['models' => $models]);

        $destination = Pdf::DEST_BROWSER;
        //$destination = Pdf::DEST_DOWNLOAD;

        $filename = 'MVP для ценностного предложения «'.$gcp_description.'».pdf';

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
                'SetTitle' => ['MVP для ценностного предложения «'.$gcp_description.'»'],
                'SetHeader' => ['<div style="color: #3c3c3c;">MVP для ценностного предложения «'.$gcp_description.'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
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
            return $this->redirect(['index', 'id' => $model->getBasicConfirmId()]);
        }

        PatternHttpException::noData();
    }


    /**
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @return Mvps|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id, bool $isOnlyNotDelete = true): ?Mvps
    {
        if (!$isOnlyNotDelete) {
            $model = Mvps::find(false)
                ->andWhere(['id' => $id])
                ->one();

        } else {
            $model = Mvps::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
