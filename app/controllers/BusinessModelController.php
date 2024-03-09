<?php

namespace app\controllers;

use app\models\ClientSettings;
use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\forms\CacheForm;
use app\models\forms\FormCreateBusinessModel;
use app\models\Gcps;
use app\models\PatternHttpException;
use app\models\Problems;
use app\models\Mvps;
use app\models\Projects;
use app\models\Segments;
use app\models\User;
use app\models\UserAccessToProjects;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use Yii;
use app\models\BusinessModel;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use kartik\mpdf\Pdf;
use yii\web\Response;

/**
 * Контроллер с методами для создания, редактирования
 * и получения информации по бизнес-модели
 *
 * Class BusinessModelController
 * @package app\controllers
 */
class BusinessModelController extends AppUserPartController
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

        if ($action->id === 'index'){

            /** @var $confirmMvp ConfirmMvp */
            $confirmMvp = ConfirmMvp::find(false)
                ->andWhere(['id' => (int)Yii::$app->request->get('id')])
                ->one();

            if (!$confirmMvp) {
                PatternHttpException::noData();
            }

            /** @var $mvp Mvps */
            $mvp = Mvps::find(false)
                ->andWhere(['id' => $confirmMvp->getMvpId()])
                ->one();

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $mvp->getProjectId()])
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

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                    return parent::beforeAction($action);
                }

                PatternHttpException::noAccess();
            }

            if (User::isUserExpert(Yii::$app->user->identity['username'])) {

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

        }elseif ($action->id === 'update'){

            $model = BusinessModel::findOne((int)Yii::$app->request->get('id'));
            $project = $model->project;

            if ($project->getUserId() === $currentUser->getId()){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'create'){

            $confirmMvp = ConfirmMvp::findOne((int)Yii::$app->request->get('id'));
            if (!$confirmMvp) {
                PatternHttpException::noData();
            }

            $project = $confirmMvp->mvp->project;

            if ($project->getUserId() === $currentUser->getId()){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'mpdf-business-model'){

            $model = BusinessModel::findOne((int)Yii::$app->request->get('id'));
            if (!$model) {
                PatternHttpException::noData();
            }
            $project = $model->project;

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

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
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
    public function actionIndex (int $id)
    {
        /** @var $model BusinessModel */
        $model = BusinessModel::find(false)
            ->andWhere(['basic_confirm_id' => $id])
            ->one();

        if (!$model) {
            return $this->redirect(['/business-model/instruction', 'id' => $id]);
        }

        /** @var $confirmMvp ConfirmMvp */
        $confirmMvp = ConfirmMvp::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $mvp Mvps */
        $mvp = Mvps::find(false)
            ->andWhere(['id' => $confirmMvp->getMvpId()])
            ->one();

        /** @var $confirmGcp ConfirmGcp */
        $confirmGcp = ConfirmGcp::find(false)
            ->andWhere(['id' => $mvp->getConfirmGcpId()])
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

        return $this->render('index', [
            'model' => $model,
            'confirmMvp' => $confirmMvp,
            'mvp' => $mvp,
            'confirmGcp' => $confirmGcp,
            'gcp' => $gcp,
            'confirmProblem' => $confirmProblem,
            'problem' => $problem,
            'confirmSegment' => $confirmSegment,
            'segment' => $segment,
            'project' => $project,
        ]);
    }


    /**
     * @param int $id
     * @return string|Response
     */
    public function actionInstruction (int $id)
    {
        $model = BusinessModel::findOne(['basic_confirm_id' => $id]);
        if ($model) {
            return $this->redirect(['/business-model/index', 'id' => $id]);
        }

        return $this->render('index_first', [
            'confirmMvp' => ConfirmMvp::findOne($id),
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
     * @return bool
     */
    public function actionSaveCacheCreationForm(int $id): bool
    {
        $confirmMvp = ConfirmMvp::findOne($id);
        $cachePath = FormCreateBusinessModel::getCachePath($confirmMvp->hypothesis);
        $cacheName = 'formCreateHypothesisCache';

        if(Yii::$app->request->isAjax) {

            $cache = new CacheForm();
            $cache->setCache($cachePath, $cacheName);
        }
        return false;
    }


    /**
     * @param $id
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function actionCreate($id)
    {
        if(Yii::$app->request->isAjax) {

            $confirmMvp = ConfirmMvp::findOne($id);
            $model = new FormCreateBusinessModel($confirmMvp->hypothesis);
            $model->setBasicConfirmId($id);

            $mvp = Mvps::findOne($confirmMvp->getMvpId());
            $gcp = Gcps::findOne($mvp->getGcpId());
            $segment = Segments::findOne($mvp->getSegmentId());

            if ($model->load(Yii::$app->request->post())) {
                if ($businessModel = $model->create()) {

                    $response = [
                        'success' => true,
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                            'model' => $businessModel, 'segment' => $segment, 'gcp' => $gcp,
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
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = $this->findModel($id);
            $confirmMvp = $model->confirmMvp;
            $gcp = $model->gcp;
            $segment = $model->segment;

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {

                    $response = [
                        'renderAjax' => $this->renderAjax('_index_ajax', [
                            'model' => BusinessModel::findOne(['basic_confirm_id' => $confirmMvp->getId()]),
                            'segment' => $segment,
                            'gcp' => $gcp,
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
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionEnableExpertise(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $model = $this->findModel($id);
            $confirmMvp = ConfirmMvp::findOne($model->getConfirmMvpId());
            $gcp = $model->gcp;
            $segment = $model->segment;

            if ($model->allowExpertise()) {

                $response = [
                    'renderAjax' => $this->renderAjax('_index_ajax', [
                        'model' => BusinessModel::findOne(['basic_confirm_id' => $confirmMvp->getId()]),
                        'segment' => $segment,
                        'gcp' => $gcp,
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
     * @return mixed
     * @throws NotFoundHttpException
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws InvalidConfigException
     */
    public function actionMpdfBusinessModel(int $id) {

        $model = $this->findModel($id);

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('viewpdf', ['model' => $model]);

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
     * @param int $id
     * @return BusinessModel|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): ?BusinessModel
    {
        if (($model = BusinessModel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



}
