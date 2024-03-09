<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который содержит информацию о добавленных параметрах
 * сравнения аналогичных продуктов для отчета маркетолога
 * на этапах подтверждения гипотез
 *
 * Class ContractorTaskSimilarProductParams
 * @package app\models
 *
 * @property int $id                                Идентификатор записи
 * @property int $task_id                           Идентификатор задачи
 * @property string $name                           Наименование параметра
 * @property int $created_at                        Дата создания
 * @property int $updated_at                        Дата редактирования
 * @property int|null $deleted_at                   Дата удаления
 *
 * @property ContractorTasks $task                  Объект задачи
 */
class ContractorTaskSimilarProductParams extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_task_similar_product_params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['task_id', 'name'], 'required'],
            ['name', 'string', 'max' => 255],
            [['task_id', 'created_at', 'updated_at', 'deleted_at'], 'integer']
        ];
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }

    /**
     * @param int|null $deleted_at
     */
    public function setDeletedAt(?int $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }
}