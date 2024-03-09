<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс, который хранит историю изменения статусов задач поставленных исполнителям проектов
 *
 * Class ContractorTaskHistory
 * @package app\models
 *
 * @property int $id                            Идентификатор записи
 * @property int $task_id                       Идентификатор задачи
 * @property int $old_status                    Старый статус задачи
 * @property int $new_status                    Новый статус задачи
 * @property string|null $comment               Комментарий
 * @property int $created_at                    Дата изменения статуса задачи
 *
 * @property ContractorTasks $task              Объект задачи
 */
class ContractorTaskHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_task_histories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['task_id', 'old_status', 'new_status'], 'required'],
            [['task_id', 'created_at', 'old_status', 'new_status'], 'integer'],
            [['old_status', 'new_status'], 'in', 'range' => [
                ContractorTasks::TASK_STATUS_NEW,
                ContractorTasks::TASK_STATUS_REJECTED,
                ContractorTasks::TASK_STATUS_COMPLETED,
                ContractorTasks::TASK_STATUS_PROCESS,
                ContractorTasks::TASK_STATUS_RETURNED,
                ContractorTasks::TASK_STATUS_READY,
                ContractorTasks::TASK_STATUS_DELETED,
            ]],
            [['comment'], 'string', 'max' => '2000'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(ContractorTasks::class, ['id' => 'task_id']);
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
    public function getTaskId(): int
    {
        return $this->task_id;
    }

    /**
     * @param int $task_id
     */
    public function setTaskId(int $task_id): void
    {
        $this->task_id = $task_id;
    }

    /**
     * @return int
     */
    public function getOldStatus(): int
    {
        return $this->old_status;
    }

    /**
     * @param int $old_status
     */
    public function setOldStatus(int $old_status): void
    {
        $this->old_status = $old_status;
    }

    /**
     * @return int
     */
    public function getNewStatus(): int
    {
        return $this->new_status;
    }

    /**
     * @param int $new_status
     */
    public function setNewStatus(int $new_status): void
    {
        $this->new_status = $new_status;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment ?: '';
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }
}