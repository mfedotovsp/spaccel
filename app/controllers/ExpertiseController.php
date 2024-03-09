<?php


namespace app\controllers;


use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\DataForUserListExpertise;
use app\models\Expertise;
use app\models\ExpertType;
use app\models\forms\expertise\FormExpertiseManyAnswer;
use app\models\forms\expertise\FormExpertiseSingleAnswer;
use app\models\Gcps;
use app\models\interfaces\ConfirmationInterface;
use app\models\Mvps;
use app\models\Problems;
use app\models\ProjectCommunications;
use app\models\Projects;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\User;
use app\models\UserAccessToProjects;
use yii\web\Response;
use Yii;


/**
 * Контроллер для создания, редактирования
 * и вывода данных экспертиз
 *
 * Class ExpertiseController
 * @package app\controllers
 */
class ExpertiseController extends AppUserPartController
{

    /**
     * Для эксперта вывести список выбора доступных экспертиз
     * по типам деятельности эксперта на проекте
     *
     * Для остальных вывести готовые экспертизы всех экспертов
     * по данному этапу, если такие есть
     *
     * @param $stage string
     * @param $stageId int
     * @return array|bool
     */
    public function actionGetList(string $stage, int $stageId)
    {
        if (Yii::$app->request->isAjax) {

            // Инициализируем дирректорию с json-файлами содержащие вариантов ответов для разных форм экспертизы
            Yii::setAlias('@dirDataFormExpertise', '../models/forms/expertise/answerOptions');

            if (User::isUserExpert(Yii::$app->user->identity['username'])) {

                $expert = User::findOne(Yii::$app->user->getId());

                if ($stage === StageExpertise::getList()[StageExpertise::PROJECT]) {
                    /** @var $hypothesis Projects */
                    $hypothesis = Projects::find(false)
                        ->andWhere(['id' => $stageId])
                        ->one();

                    $userAccessToProject = $expert->findUserAccessToProject($hypothesis->getId());
                } else {
                    $stageClass = StageExpertise::getClassByStage($stage);
                    $interfaces = class_implements($stageClass);
                    /**
                     * @var Segments|Problems|Gcps|Mvps $hypothesis
                     */
                    if (!isset($interfaces[ConfirmationInterface::class])) {
                        $hypothesis = $stageClass::find(false)
                            ->andWhere(['id' => $stageId])
                            ->one();
                    } else {
                        /** @var $confirmHypothesis ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp */
                        $confirmHypothesis = $stageClass::find(false)
                            ->andWhere(['id' => $stageId])
                            ->one();

                        if ($stageClass === ConfirmSegment::class) {
                            $hypothesis = Segments::find(false)
                                ->andWhere(['id' => $confirmHypothesis->getSegmentId()])
                                ->one();
                        }

                        if ($stageClass === ConfirmProblem::class) {
                            $hypothesis = Problems::find(false)
                                ->andWhere(['id' => $confirmHypothesis->getProblemId()])
                                ->one();
                        }

                        if ($stageClass === ConfirmGcp::class) {
                            $hypothesis = Gcps::find(false)
                                ->andWhere(['id' => $confirmHypothesis->getGcpId()])
                                ->one();
                        }

                        if ($stageClass === ConfirmMvp::class) {
                            $hypothesis = Mvps::find(false)
                                ->andWhere(['id' => $confirmHypothesis->getMvpId()])
                                ->one();
                        }
                    }
                    $userAccessToProject = $expert->findUserAccessToProject($hypothesis->getProjectId());
                }

                if (empty($hypothesis->getDeletedAt())) {
                    /**
                     * @var UserAccessToProjects $userAccessToProject
                     * @var ProjectCommunications $communication
                     */
                    $communication = $userAccessToProject->communication;
                    $typesAccessToExpertise = $communication->typesAccessToExpertise;
                    $types = ExpertType::getListTypes(null, $typesAccessToExpertise->getTypes());

                    $response = [
                        'headerContent' => 'Экспертиза по этапу: ' . StageExpertise::getTitle($stage, $stageId),
                        'renderList' => $this->renderAjax('list_expertise', [
                            'types' => $types, 'stage' => $stage, 'stageId' => $stageId,
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;

                }

                /** @var Expertise[] $models */
                $models = Expertise::find()->andWhere([
                    'stage' => array_search($stage, StageExpertise::getList(), false),
                    'stage_id' => $stageId,
                    'completed' => Expertise::COMPLETED,
                    'expert_id' => $expert->getId()
                ])->orderBy('updated_at DESC')->all();

                $stageClass = StageExpertise::getClassByStage($stage);
                $interfaces = class_implements($stageClass);

                $data = array(); // Массив с данными по экспертизе данного этапа проекта

                foreach ($models as $i => $model) {
                    $data[$i] = new DataForUserListExpertise(
                        $model->getId(),
                        $model->getUpdatedAt(),
                        $model->getTypeExpert(),
                        $model->expert->getUsername(),
                        $model->getGeneralEstimationByOne(),
                        $model->getComment(),
                        !isset($interfaces[ConfirmationInterface::class]) ? new FormExpertiseSingleAnswer($model) : new FormExpertiseManyAnswer($model)
                    );
                }

                $response = [
                    'headerContent' => 'Экспертиза по этапу: ' . StageExpertise::getTitle($stage, $stageId),
                    'renderList' => $this->renderAjax('user_list_expertise', [
                        'stage' => $stage, 'stageId' => $stageId, 'data' => $data
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            /** @var Expertise[] $models */
            $models = Expertise::find()->andWhere([
                'stage' => array_search($stage, StageExpertise::getList(), false),
                'stage_id' => $stageId,
                'completed' => Expertise::COMPLETED
            ])->orderBy('updated_at DESC')->all();

            $stageClass = StageExpertise::getClassByStage($stage);
            $interfaces = class_implements($stageClass);

            $data = array(); // Массив с данными по экспертизе данного этапа проекта

            foreach ($models as $i => $model) {
                $data[$i] = new DataForUserListExpertise(
                    $model->getId(),
                    $model->getUpdatedAt(),
                    $model->getTypeExpert(),
                    $model->expert->getUsername(),
                    $model->getGeneralEstimationByOne(),
                    $model->getComment(),
                    !isset($interfaces[ConfirmationInterface::class]) ? new FormExpertiseSingleAnswer($model) : new FormExpertiseManyAnswer($model)
                );
            }

            $response = [
                'headerContent' => 'Экспертиза по этапу: ' . StageExpertise::getTitle($stage, $stageId),
                'renderList' => $this->renderAjax('user_list_expertise', [
                    'stage' => $stage, 'stageId' => $stageId, 'data' => $data
                ]),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Вывести форму для проведения
     * экспертизы по этапу проекта
     *
     * @param string $stage
     * @param int $stageId
     * @param int $type
     * @param int|bool $completed
     * @return array|bool
     */
    public function actionGetForm(string $stage, int $stageId, int $type, $completed = false)
    {
        if (Yii::$app->request->isAjax && User::isUserExpert(Yii::$app->user->identity['username'])) {

            // Инициализируем дирректорию с json-файлами содержащие вариантов ответов для разных форм экспертизы
            Yii::setAlias('@dirDataFormExpertise', '../models/forms/expertise/answerOptions');

            $expert = User::findOne(Yii::$app->user->getId());
            $stageClass = StageExpertise::getClassByStage($stage);
            $interfaces = class_implements($stageClass);

            /** @var UserAccessToProjects $userAccessToProject */
            if ($stage === StageExpertise::getList()[StageExpertise::PROJECT]) {
                $project = Projects::findOne($stageId);
                $userAccessToProject = $expert->findUserAccessToProject($stageId);
            } else {
                /**
                 * @var Segments|Problems|Gcps|Mvps $hypothesis
                 */
                $hypothesis = !isset($interfaces[ConfirmationInterface::class]) ? $stageClass::findOne($stageId) : $stageClass::findOne($stageId)->hypothesis;
                $project = $hypothesis->project;
                $userAccessToProject = $expert->findUserAccessToProject($project->getId());
            }

            $expertise = Expertise::findOne([
                'stage' => array_search($stage, StageExpertise::getList(), false),
                'stage_id' => $stageId,
                'expert_id' => $expert->getId(),
                'type_expert' => $type,
                'communication_id' => $userAccessToProject->getCommunicationId()
            ]);

            if (!$expertise) {
                $expertise = new Expertise();
                $expertise->setStage(array_search($stage, StageExpertise::getList(), false));
                $expertise->setStageId($stageId);
                $expertise->setExpertId($expert->getId());
                $expertise->setUserId($project->getUserId());
                $expertise->setTypeExpert($type);
                $expertise->setCommunicationId($userAccessToProject->getCommunicationId());
                $expertise->setEstimation('');
                $expertise->setComment('');
                $expertise->completed = Expertise::NO_COMPLETED;
            }

            $model = !isset($interfaces[ConfirmationInterface::class]) ? new FormExpertiseSingleAnswer($expertise) : new FormExpertiseManyAnswer($expertise);

            // Сохранить форму экспертизы
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate() && $model->saveRecord($completed)) {
                    // После сохранения вернуться к списку экспертиз по типам деятельности
                    return $this->actionGetList($stage, $stageId);
                }
            }

            // Показать форму экспертизы
            if (!isset($interfaces[ConfirmationInterface::class])) {
                $response = [
                    'renderAjax' => $this->renderAjax('hypothesis/form_expertise_single_answer', [
                        'model' => $model, 'stage' => $stage, 'stageId' => $stageId
                    ]),
                ];
            } else {
                $response = [
                    'renderAjax' => $this->renderAjax('confirm/form_expertise_many_answer', [
                        'model' => $model, 'stage' => $stage, 'stageId' => $stageId
                    ]),
                ];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }

}