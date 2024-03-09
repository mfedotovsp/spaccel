<?php


namespace app\controllers;

use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\forms\FormCreateQuestion;
use app\models\forms\FormUpdateQuestion;
use app\models\ConfirmSegment;
use app\models\PatternHttpException;
use app\models\QuestionsConfirmGcp;
use app\models\QuestionsConfirmMvp;
use app\models\QuestionsConfirmProblem;
use app\models\QuestionsConfirmSegment;
use app\models\StageConfirm;
use Throwable;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;
use Yii;

/**
 * Контроллер с методами создания, редактирования и получения информации
 * по вопросам для интервью с респондентами при подтверждении гипотезы
 *
 * Class QuestionsController
 * @package app\controllers
 */
class QuestionsController extends AppUserPartController
{

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {

        if ($action->id === 'create'){

            $confirm = self::getConfirm(Yii::$app->request->get('stage'), (int)Yii::$app->request->get('id'));
            $hypothesis = $confirm->hypothesis;
            $project = $hypothesis->project;

            if ($project->getUserId() === Yii::$app->user->getId()){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif (in_array($action->id, ['delete', 'update'])){

            $question = self::getModel(Yii::$app->request->get('stage'), (int)Yii::$app->request->get('id'));
            $confirm = $question->confirm;
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
     * @param int $stage
     * @param int $id
     * @return array|bool
     */
    public function actionCreate(int $stage, int $id)
    {
        if(Yii::$app->request->isAjax) {

            $form = new FormCreateQuestion();
            $form->setConfirmId($id);
            $model = self::getCreateModel($stage);

            if ($form->load(Yii::$app->request->post())){
                if ($result = $form->create($model)){

                    $response = [
                        'model' => $result['model'],
                        'questions' => $result['questions'],
                        'queryQuestions' => $result['queryQuestions'],
                        'ajax_questions_confirm' => $this->renderAjax('list_questions', ['questions' => $result['questions']]),
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
     * @param int $stage
     * @param int $id
     * @return array|bool
     */
    public function actionGetFormUpdate(int $stage, int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = self::getModel($stage, $id);
            $form = new FormUpdateQuestion($model);
            $confirm = $form->confirm;
            $questions = $confirm->questions;

            $response = [
                'ajax_questions_confirm' => $this->renderAjax('list_questions', ['questions' => $questions]),
                'renderAjax' => $this->renderAjax('form_update', ['model' => $form]),
            ];
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
     */
    public function actionUpdate(int $stage, int $id)
    {
        if (Yii::$app->request->isAjax) {

            $model = self::getModel($stage, $id);
            $form = new FormUpdateQuestion($model);

            if ($form->load(Yii::$app->request->post())) {
                if ($result = $form->update()){

                    $response = [
                        'model' => $result['model'],
                        'questions' => $result['questions'],
                        'queryQuestions' => $result['queryQuestions'],
                        'ajax_questions_confirm' => $this->renderAjax('list_questions', ['questions' => $result['questions']]),
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
     * @param int $stage
     * @param int $id
     * @return bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionChangeStatus(int $stage, int $id): bool
    {
        if (Yii::$app->request->isAjax) {

            $model = self::getModel($stage, $id);
            $model->changeStatus();

            if ($model->update()){
                return true;
            }
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @return array|bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(int $stage, int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = self::getModel($stage, $id);

            if ($data = $model->deleteAndGetData()){

                $response = [
                    'questions' => $data['questions'],
                    'queryQuestions' => $data['queryQuestions'],
                    'ajax_questions_confirm' => $this->renderAjax('list_questions', ['questions' => $data['questions']]),
                ];
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
     * @return array|bool
     */
    public function actionGetQueryQuestions(int $stage, int $id)
    {
        $confirm = self::getConfirm($stage, $id);
        $questions = $confirm->questions;

        if(Yii::$app->request->isAjax) {
            $response = ['ajax_questions_confirm' => $this->renderAjax('list_questions', ['questions' => $questions])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @return QuestionsConfirmGcp|QuestionsConfirmMvp|QuestionsConfirmProblem|QuestionsConfirmSegment|bool|null
     */
    private static function getModel(int $stage, int $id)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return QuestionsConfirmSegment::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return QuestionsConfirmProblem::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return QuestionsConfirmGcp::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return QuestionsConfirmMvp::findOne($id);
        }
        return false;
    }


    /**
     * @param int $stage
     * @return QuestionsConfirmGcp|QuestionsConfirmMvp|QuestionsConfirmProblem|QuestionsConfirmSegment|bool
     */
    private static function getCreateModel(int $stage)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return new QuestionsConfirmSegment();
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new QuestionsConfirmProblem();
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return new QuestionsConfirmGcp();
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return new QuestionsConfirmMvp();
        }
        return false;
    }


    /**
     * @param int $stage
     * @param int $id
     * @return ConfirmGcp|ConfirmMvp|ConfirmProblem|ConfirmSegment|bool|null
     */
    private static function getConfirm(int $stage, int $id)
    {
        if ($stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return ConfirmSegment::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return ConfirmProblem::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return ConfirmGcp::findOne($id);
        }

        if ($stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return ConfirmMvp::findOne($id);
        }
        return false;
    }
}