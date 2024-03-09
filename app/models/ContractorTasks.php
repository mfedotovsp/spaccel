<?php

namespace app\models;

use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Класс, который хранит информацию о задачах поставленных исполнителям проектов
 *
 * Class ContractorTasks
 * @package app\models
 *
 * @property int $id                                                                    Идентификатор задачи
 * @property int $contractor_id                                                         Идентификатор исполнителя
 * @property int $project_id                                                            Идентификатор проекта
 * @property int $activity_id                                                           Идентификатор вида деятельности
 * @property int $type                                                                  Тип задачи (связан с этапом проекта, по которому необходимо выполнить задание)
 * @property int $status                                                                Статус задачи
 * @property int $hypothesis_id                                                         Идентификатор гипотезы (этапа проекта, по которому необходимо выполнить задание, т.е. например id проекта для создания сегментов, id подтверждения сегмента для подтверждения сегмента и т.д.)
 * @property string $description                                                        Описание задачи
 * @property int $created_at                                                            Дата создания
 * @property int $updated_at                                                            Дата изменения
 *
 * @property User $contractor                                                           Объект исполнителя проекта
 * @property Projects $project                                                          Объект проекта
 * @property ContractorActivities $activity                                             Объект вида деятельности
 * @property Projects|ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp $hypothesis   Объект ссылки этапа проекта
 * @property ContractorTaskHistory[] $histories                                         История изменения статусов задачи
 * @property ContractorTaskProducts[] $products                                         Продукты, созданные маркетологом
 * @property ContractorTaskSimilarProducts[] $similarProducts                           Продукты-аналоги, созданные маркетологом
 * @property ContractorTaskFiles[] $files                                               Загруженные файлы в заданиях исполнителей
 */
class ContractorTasks extends ActiveRecord
{
    public const TASK_STATUS_NEW = 12974543; // Новое задание
    public const TASK_STATUS_REJECTED = 9603574; // Задание отозвано проктантом
    public const TASK_STATUS_PROCESS = 4581456; // Исполнитель взял задание в работу
    public const TASK_STATUS_COMPLETED = 5603465; // Исполнитель завершил задание
    public const TASK_STATUS_RETURNED = 3366557; // Проектант вернул задание в доработку
    public const TASK_STATUS_READY = 3863285; // Проектант перевел задание в статус "Готово"
    public const TASK_STATUS_DELETED = 4477665; // При удалении гипотезы, к которой относится задание, статус меняется на "Удалено"

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'project_id', 'type', 'hypothesis_id', 'description', 'activity_id'], 'required'],
            [['contractor_id', 'project_id', 'type', 'hypothesis_id', 'activity_id'], 'integer'],
            ['status', 'default', 'value' => self::TASK_STATUS_NEW],
            ['status', 'in', 'range' => [
                self::TASK_STATUS_NEW,
                self::TASK_STATUS_REJECTED,
                self::TASK_STATUS_COMPLETED,
                self::TASK_STATUS_PROCESS,
                self::TASK_STATUS_RETURNED,
                self::TASK_STATUS_READY,
                self::TASK_STATUS_DELETED,
            ]],
            ['type', 'in', 'range' => [
                StageExpertise::SEGMENT,
                StageExpertise::CONFIRM_SEGMENT,
                StageExpertise::PROBLEM,
                StageExpertise::CONFIRM_PROBLEM,
                StageExpertise::GCP,
                StageExpertise::CONFIRM_GCP,
                StageExpertise::MVP,
                StageExpertise::CONFIRM_MVP,
            ]],
            [['description'], 'string', 'max' => '2000'],
        ];
    }

    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, function() {
            $this->sendCommunication();
        });
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }


    /**
     * @return ActiveRecord|null
     */
    public function getHypothesis(): ?ActiveRecord
    {
        if ($this->getType() === StageExpertise::SEGMENT) {
            return Projects::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_SEGMENT) {
            return ConfirmSegment::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::PROBLEM) {
            return ConfirmSegment::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_PROBLEM) {
            return ConfirmProblem::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::GCP) {
            return ConfirmProblem::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_GCP) {
            return ConfirmGcp::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::MVP) {
            return ConfirmGcp::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_MVP) {
            return ConfirmMvp::find(false)
                ->andWhere(['id' => $this->getHypothesisId()])
                ->one();
        }

        return null;
    }


    /**
     * @return ActiveQuery
     */
    public function getContractor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getActivity(): ActiveQuery
    {
        return $this->hasOne(ContractorActivities::class, ['id' => 'activity_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHistories(): ActiveQuery
    {
        return $this->hasMany(ContractorTaskHistory::class, ['task_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(ContractorTaskProducts::class, ['task_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSimilarProducts(): ActiveQuery
    {
        return $this->hasMany(ContractorTaskSimilarProducts::class, ['task_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(ContractorTaskFiles::class, ['task_id' => 'id']);
    }

    /**
     * @return ActiveRecord|null
     */
    public function getProject(): ?ActiveRecord
    {
        return Projects::find(false)
            ->andWhere(['id' => $this->getProjectId()])
            ->one();
    }

    /**
     * @return int|null
     */
    public function getTypeCommunication(): ?int
    {
        if ($this->getType() === StageExpertise::SEGMENT) {
            return ContractorCommunicationTypes::USER_APPOINTS_SEGMENT_TASK_CONTRACTOR;
        }
        if ($this->getType() === StageExpertise::CONFIRM_SEGMENT) {
            return ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_SEGMENT_TASK_CONTRACTOR;
        }
        if ($this->getType() === StageExpertise::PROBLEM) {
            return ContractorCommunicationTypes::USER_APPOINTS_PROBLEM_TASK_CONTRACTOR;
        }
        if ($this->getType() === StageExpertise::CONFIRM_PROBLEM) {
            return ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_PROBLEM_TASK_CONTRACTOR;
        }
        if ($this->getType() === StageExpertise::GCP) {
            return ContractorCommunicationTypes::USER_APPOINTS_GCP_TASK_CONTRACTOR;
        }
        if ($this->getType() === StageExpertise::CONFIRM_GCP) {
            return ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_GCP_TASK_CONTRACTOR;
        }
        if ($this->getType() === StageExpertise::MVP) {
            return ContractorCommunicationTypes::USER_APPOINTS_MVP_TASK_CONTRACTOR;
        }
        if ($this->getType() === StageExpertise::CONFIRM_MVP) {
            return ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_MVP_TASK_CONTRACTOR;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function sendCommunication(): bool
    {
        if (!$typeCommunication = $this->getTypeCommunication()) {
            return false;
        }

        $communication = new ContractorCommunications();
        $communication->setParams(
            $this->getContractorId(),
            $this->getProjectId(),
            $this->getActivityId(),
            $typeCommunication,
            $this->getType(),
            $this->getHypothesisId()
        );
        return $communication->save();
    }

    /**
     * Получить название этапа проекта
     *
     * @return string
     */
    public function getNameStage(): string
    {
        switch ($this->getType()) {
            case StageExpertise::SEGMENT:
                return 'генерация гипотезы целевого сегмента';
            case StageExpertise::CONFIRM_SEGMENT:
                return 'подтверждение гипотезы целевого сегмента';
            case StageExpertise::PROBLEM:
                return 'генерация гипотезы проблемы сегмента';
            case StageExpertise::CONFIRM_PROBLEM:
                return 'подтверждение гипотезы проблемы сегмента';
            case StageExpertise::GCP:
                return 'разработка гипотезы ценностного предложения';
            case StageExpertise::CONFIRM_GCP:
                return 'подтверждение гипотезы ценностного предложения';
            case StageExpertise::MVP:
                return 'разработка MVP';
            case StageExpertise::CONFIRM_MVP:
                return 'подтверждение MVP';
            default:
                return '';
        }
    }

    /**
     * @return string
     */
    public function getStageUrl(): string
    {
        $isContractor = User::isUserContractor(Yii::$app->user->identity['username']);

        switch ($this->getType()) {
            case StageExpertise::SEGMENT:
                return Url::to($isContractor ? ['/contractor/segments/task', 'id' => $this->getId()] : ['/segments/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_SEGMENT:
                return Url::to($isContractor ? ['/contractor/confirm-segment/task', 'id' => $this->getId()] : ['/confirm-segment/view', 'id' => $this->getHypothesisId()]);
            case StageExpertise::PROBLEM:
                return Url::to($isContractor ? ['/contractor/problems/task', 'id' => $this->getId()] : ['/problems/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_PROBLEM:
                return Url::to($isContractor ? ['/contractor/confirm-problem/task', 'id' => $this->getId()] : ['/confirm-problem/view', 'id' => $this->getHypothesisId()]);
            case StageExpertise::GCP:
                return Url::to($isContractor ? ['/contractor/gcps/task', 'id' => $this->getId()] : ['/gcps/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_GCP:
                return Url::to($isContractor ? ['/contractor/confirm-gcp/task', 'id' => $this->getId()] : ['/confirm-gcp/view', 'id' => $this->getHypothesisId()]);
            case StageExpertise::MVP:
                return Url::to($isContractor ? ['/contractor/mvps/task', 'id' => $this->getId()] : ['/mvps/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_MVP:
                return Url::to($isContractor ? ['/contractor/confirm-mvp/task', 'id' => $this->getId()] : ['/confirm-mvp/view', 'id' => $this->getHypothesisId()]);
            default:
                return '';
        }
    }

    /**
     * Получить ссылку на этап проекта
     *
     * @param bool $goTaskPage
     * @return string
     */
    public function getStageLink(bool $goTaskPage = true): string
    {
        switch ($this->getType()) {
            case StageExpertise::SEGMENT:
                return Html::a('генерация гипотезы целевого сегмента', $goTaskPage ? ['/contractor/segments/task', 'id' => $this->getId()] : ['/segments/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_SEGMENT:
                return Html::a('подтверждение гипотезы целевого сегмента', $goTaskPage ? ['/contractor/confirm-segment/task', 'id' => $this->getId()] : ['/confirm-segment/view', 'id' => $this->getHypothesisId()]);
            case StageExpertise::PROBLEM:
                return Html::a('генерация гипотезы проблемы сегмента', $goTaskPage ? ['/contractor/problems/task', 'id' => $this->getId()] : ['/problems/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_PROBLEM:
                return Html::a('подтверждение гипотезы проблемы сегмента', $goTaskPage ? ['/contractor/confirm-problem/task', 'id' => $this->getId()] : ['/confirm-problem/view', 'id' => $this->getHypothesisId()]);
            case StageExpertise::GCP:
                return Html::a('разработка гипотезы ценностного предложения', $goTaskPage ? ['/contractor/gcps/task', 'id' => $this->getId()] : ['/gcps/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_GCP:
                return Html::a('подтверждение гипотезы ценностного предложения', $goTaskPage ? ['/contractor/confirm-gcp/task', 'id' => $this->getId()] : ['/confirm-gcp/view', 'id' => $this->getHypothesisId()]);
            case StageExpertise::MVP:
                return Html::a('разработка MVP', $goTaskPage ? ['/contractor/mvps/task', 'id' => $this->getId()] : ['/mvps/index', 'id' => $this->getHypothesisId()]);
            case StageExpertise::CONFIRM_MVP:
                return Html::a('подтверждение MVP', $goTaskPage ? ['/contractor/confirm-mvp/task', 'id' => $this->getId()] : ['/confirm-mvp/view', 'id' => $this->getHypothesisId()]);
            default:
                return '';
        }
    }

    /**
     * @param int $newStatus
     * @param string|null $comment
     * @return bool
     * @throws \Throwable
     */
    public function changeStatus(int $newStatus, string $comment = null): bool
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $history = new ContractorTaskHistory();
            $history->setTaskId($this->getId());
            $history->setOldStatus($this->getStatus());
            $history->setNewStatus($newStatus);
            if ($comment) {
                $history->setComment($comment);
            }

            $history->save();
            $this->setStatus($newStatus);
            if ($this->update()) {
                $communication = new ContractorCommunications();
                $communication->setParams(
                    User::isUserContractor(Yii::$app->user->identity['username']) ?
                        $this->project->getUserId() :
                        $this->getContractorId(),
                    $this->getProjectId(),
                    $this->getActivityId(),
                    User::isUserContractor(Yii::$app->user->identity['username']) ?
                        ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK :
                        ContractorCommunicationTypes::USER_CHANGE_STATUS_TASK,
                    $this->getType(),
                    $this->getHypothesisId()
                );
                $communication->save();
            }
            $transaction->commit();
            return true;

        } catch (Exception $exception) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @param int $type
     * @param int $hypothesisId
     * @return bool
     * @throws \Throwable
     */
    public static function deleteByParams(int $type, int $hypothesisId): bool
    {
        $tasks = self::findAll(['type' => $type, 'hypothesis_id' => $hypothesisId]);
        foreach ($tasks as $task) {
            if (!$task->changeStatus(self::TASK_STATUS_DELETED)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param int $type
     * @param int $hypothesisId
     * @return bool
     * @throws \Throwable
     */
    public static function recoveryByParams(int $type, int $hypothesisId): bool
    {
        $tasks = self::findAll(['type' => $type, 'hypothesis_id' => $hypothesisId]);
        foreach ($tasks as $task) {
            /** @var ContractorTaskHistory $lastHistory */
            $lastHistory = ContractorTaskHistory::find()
                ->andWhere(['task_id' => $task->getId()])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            if (!$task->changeStatus($lastHistory->getOldStatus())) {
                return false;
            }
        }
        return true;
    }

    /**
     * Получить ссылку на страницу задания
     *
     * @return string
     */
    public function getLinkToTaskPage(): string
    {
        switch ($this->getType()) {
            case StageExpertise::SEGMENT:
                return Url::to(['/contractor/segments/task', 'id' => $this->getId()]);
            case StageExpertise::CONFIRM_SEGMENT:
                return Url::to(['/contractor/confirm-segment/task', 'id' => $this->getId()]);
            case StageExpertise::PROBLEM:
                return Url::to(['/contractor/problems/task', 'id' => $this->getId()]);
            case StageExpertise::CONFIRM_PROBLEM:
                return Url::to(['/contractor/confirm-problem/task', 'id' => $this->getId()]);
            case StageExpertise::GCP:
                return Url::to(['/contractor/gcps/task', 'id' => $this->getId()]);
            case StageExpertise::CONFIRM_GCP:
                return Url::to(['/contractor/confirm-gcp/task', 'id' => $this->getId()]);
            case StageExpertise::MVP:
                return Url::to(['/contractor/mvps/task', 'id' => $this->getId()]);
            case StageExpertise::CONFIRM_MVP:
                return Url::to(['/contractor/confirm-mvp/task', 'id' => $this->getId()]);
            default:
                return '#';
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getContractorId(): int
    {
        return $this->contractor_id;
    }

    /**
     * @param int $contractor_id
     */
    public function setContractorId(int $contractor_id): void
    {
        $this->contractor_id = $contractor_id;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->project_id;
    }

    /**
     * @param int $project_id
     */
    public function setProjectId(int $project_id): void
    {
        $this->project_id = $project_id;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatusToString(): string
    {
        if ($this->status === self::TASK_STATUS_NEW) {
            return 'Новое';
        }
        if ($this->status === self::TASK_STATUS_PROCESS) {
            return 'В работе';
        }
        if ($this->status === self::TASK_STATUS_COMPLETED) {
            return 'Завершено';
        }
        if ($this->status === self::TASK_STATUS_RETURNED) {
            return 'В доработке';
        }
        if ($this->status === self::TASK_STATUS_REJECTED) {
            return 'Отозвано';
        }
        if ($this->status === self::TASK_STATUS_READY) {
            return 'Готово';
        }
        if ($this->status === self::TASK_STATUS_DELETED) {
            return "Удалено";
        }

        return '';
    }

    /**
     * @param int $status
     * @return string
     */
    public static function statusToString(int $status): string
    {
        if ($status === self::TASK_STATUS_NEW) {
            return 'Новое';
        }
        if ($status === self::TASK_STATUS_PROCESS) {
            return 'В работе';
        }
        if ($status === self::TASK_STATUS_COMPLETED) {
            return 'Завершено';
        }
        if ($status === self::TASK_STATUS_RETURNED) {
            return 'В доработке';
        }
        if ($status === self::TASK_STATUS_REJECTED) {
            return 'Отозвано';
        }
        if ($status === self::TASK_STATUS_READY) {
            return 'Готово';
        }
        if ($status === self::TASK_STATUS_DELETED) {
            return "Удалено";
        }

        return '';
    }

    /**
     * @return int
     */
    public function getHypothesisId(): int
    {
        return $this->hypothesis_id;
    }

    /**
     * @param int $hypothesis_id
     */
    public function setHypothesisId(int $hypothesis_id): void
    {
        $this->hypothesis_id = $hypothesis_id;
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
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    /**
     * @return int
     */
    public function getActivityId(): int
    {
        return $this->activity_id;
    }

    /**
     * @param int $activity_id
     */
    public function setActivityId(int $activity_id): void
    {
        $this->activity_id = $activity_id;
    }
}