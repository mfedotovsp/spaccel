<?php

namespace app\models\forms;

use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\CreatorAnswersForNewRespond;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\RespondsGcp;
use app\models\RespondsMvp;
use app\models\RespondsProblem;
use app\models\RespondsSegment;
use app\models\StageExpertise;
use Yii;
use yii\base\Model;

/**
 * Форма создания задания по гипотезе для исполнителя
 *
 * @property int $contractorId                 Идентификатор исполнителя
 * @property int $projectId                    Идентификатор проекта
 * @property int $activityId                   Идентификатор вида деятельности
 * @property int $type                         Тип задачи (связан с этапом проекта, по которому необходимо выполнить задание)
 * @property int $hypothesisId                 Идентификатор гипотезы (этапа проекта, по которому необходимо выполнить задание)
 * @property string $description               Описание задачи
 * @property bool $useRespond                  Использовать респондентов предыдущего этапа
 *
 * Class FormCreateTaskHypothesis
 * @package app\models\forms
 */
class FormCreateTaskHypothesis extends Model
{
    public $contractorId;
    public $projectId;
    public $activityId;
    public $type;
    public $hypothesisId;
    public $description;
    public $useRespond = false;

    public function __construct(int $projectId, int $type, int $hypothesisId, $config = [])
    {
        $this->projectId = $projectId;
        $this->type = $type;
        $this->hypothesisId = $hypothesisId;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['contractorId', 'projectId', 'activityId', 'type', 'hypothesisId', 'description'], 'required'],
            [['contractorId', 'projectId', 'activityId', 'type', 'hypothesisId'], 'integer'],
            [['description'], 'string', 'max' => '2000'],
            ['useRespond', 'boolean']

        ];
    }

    /**
     * Возвращает, модель созданной задачи и флаг перезагрузки страницы
     * @return array
     */
    public function create(): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new ContractorTasks();
            $model->setContractorId($this->getContractorId());
            $model->setProjectId($this->getProjectId());
            $model->setActivityId($this->getActivityId());
            $model->setType($this->getType());
            $model->setHypothesisId($this->getHypothesisId());
            $model->setDescription($this->getDescription());
            $model->save();
            $this->addResponds($model);

            $transaction->commit();
            if ($this->isUseRespond() && $model->activity->getTitle() === 'Полевая работа' && in_array($model->getType(), [
                    StageExpertise::CONFIRM_PROBLEM, StageExpertise::CONFIRM_GCP, StageExpertise::CONFIRM_MVP], true)) {
                return [$model, true];
            }

            return [$model, false];
        } catch (\Exception $exception) {
            $transaction->rollBack();
            return [null, false];
        }
    }

    /**
     * @param ContractorTasks $model
     * @return void
     */
    private function addResponds(ContractorTasks $model): void
    {
        if ($respondsPreConfirm = $this->getRespondsPreConfirm($model)) {
            /**
             * @var ConfirmProblem|ConfirmGcp|ConfirmMvp $confirm
             * @var RespondsSegment[]|RespondsProblem[]|RespondsGcp[] $responds
             */
            [$confirm, $responds] = $respondsPreConfirm;
            $classRespond = self::getClassRespond($model->getType());
            foreach ($responds as $item) {
                /** @var RespondsProblem|RespondsGcp|RespondsMvp $respond */
                $respond = new $classRespond();
                $respond->setConfirmId($model->getHypothesisId());
                $respond->setName($item->getName());
                $respond->setInfoRespond($item->getInfoRespond());
                $respond->setEmail($item->getEmail());
                $respond->setPlaceInterview($item->getPlaceInterview());
                $respond->setContractorId($model->getContractorId());
                $respond->setTaskId($model->getId());
                $respond->save();

                // Добавление пустых ответов на вопросы для нового респондента
                $creatorAnswers = new CreatorAnswersForNewRespond();
                $creatorAnswers->create($respond);
            }

            $confirm->setCountRespond(array_sum([$confirm->getCountRespond(),count($responds)]));
            $confirm->save();
        }
    }

    /**
     * @param ContractorTasks $model
     * @return array
     */
    private function getRespondsPreConfirm(ContractorTasks $model): array
    {
        if ($this->isUseRespond() && $model->activity->getTitle() === 'Полевая работа' && in_array($model->getType(), [
            StageExpertise::CONFIRM_PROBLEM, StageExpertise::CONFIRM_GCP, StageExpertise::CONFIRM_MVP], true)) {

            $classConfirm = self::getClassConfirm($model->getType());

            /** @var ConfirmProblem|ConfirmGcp|ConfirmMvp $confirm */
            $confirm = $classConfirm::findOne($model->getHypothesisId());
            $hypothesis = $confirm->hypothesis;
            $preConfirm = self::getPreConfirm($hypothesis);

            $responds = [];
            foreach ($preConfirm->responds as $respond) {
                if ($respond->getContractorId() === $model->getContractorId()) {
                    $responds[] = $respond;
                }
            }
            if (!$responds) {
                return [];
            }

            return [$confirm, $responds];
        }

        return [];
    }

    /**
     * @param int $type
     * @return string
     */
    private static function getClassConfirm(int $type): string
    {
        if ($type === StageExpertise::CONFIRM_PROBLEM) {
            return ConfirmProblem::class;
        }
        if ($type === StageExpertise::CONFIRM_GCP) {
            return ConfirmGcp::class;
        }
        if ($type === StageExpertise::CONFIRM_MVP) {
            return ConfirmMvp::class;
        }
        return '';
    }


    /**
     * @param Problems|Gcps|Mvps $hypothesis
     * @return ConfirmGcp|ConfirmProblem|ConfirmSegment|null
     */
    private static function getPreConfirm($hypothesis)
    {
        if ($hypothesis instanceof Problems) {
            return ConfirmSegment::findOne($hypothesis->getBasicConfirmId());
        }
        if ($hypothesis instanceof Gcps) {
            return ConfirmProblem::findOne($hypothesis->getBasicConfirmId());
        }
        if ($hypothesis instanceof Mvps) {
            return ConfirmGcp::findOne($hypothesis->getBasicConfirmId());
        }
        return null;
    }

    /**
     * @param int $type
     * @return string
     */
    private static function getClassRespond(int $type): string
    {
        if ($type === StageExpertise::CONFIRM_PROBLEM) {
            return RespondsProblem::class;
        }
        if ($type === StageExpertise::CONFIRM_GCP) {
            return RespondsGcp::class;
        }
        if ($type === StageExpertise::CONFIRM_MVP) {
            return RespondsMvp::class;
        }
        return '';
    }


    /**
     * @return int
     */
    public function getContractorId(): int
    {
        return $this->contractorId;
    }

    /**
     * @param int $contractorId
     */
    public function setContractorId(int $contractorId): void
    {
        $this->contractorId = $contractorId;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @return int
     */
    public function getActivityId(): int
    {
        return $this->activityId;
    }

    /**
     * @param int $activityId
     */
    public function setActivityId(int $activityId): void
    {
        $this->activityId = $activityId;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getHypothesisId(): int
    {
        return $this->hypothesisId;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isUseRespond(): bool
    {
        return $this->useRespond;
    }

    /**
     * @param bool $useRespond
     */
    public function setUseRespond(bool $useRespond): void
    {
        $this->useRespond = $useRespond;
    }

}