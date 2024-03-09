<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Класс, который содержит информацию о продуктах аналогах
 * для отчета маркетолога на этапах подтверждения гипотез
 *
 * Class ContractorTaskSimilarProducts
 * @package app\models
 *
 * @property int $id                                Идентификатор записи
 * @property int $contractor_id                     Идентификатор исполнителя
 * @property int $task_id                           Идентификатор задачи
 * @property string $name                           Наименование продукта
 * @property int $ownership_cost                    Стоимость владения продуктом
 * @property int $price                             Цена продукта
 * @property string|null $params                    Описание продукта по добавленным параметрам сравнения
 * @property int $created_at                        Дата создания
 * @property int $updated_at                        Дата редактирования
 *
 * @property ContractorTasks $task                  Объект задачи
 * @property User $contractor                       Объект исполнителя
 */
class ContractorTaskSimilarProducts extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_task_similar_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'task_id', 'name', 'ownership_cost', 'price'], 'required'],
            [['contractor_id', 'task_id', 'created_at', 'updated_at'], 'integer'],
            [['ownership_cost', 'price'], 'integer', 'min' => 1, 'max' => 1000000000],
            [['name'], 'string', 'max' => 255],
            ['name', 'trim'],
            ['params', 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Наименование продукта',
            'price' => 'Цена продукта (в рублях)',
            'ownership_cost' => 'Стоимость владения (в рублях)',
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
     * @return ActiveQuery
     */
    public function getContractor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
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
    public function getOwnershipCost(): int
    {
        return $this->ownership_cost;
    }

    /**
     * @param int $ownership_cost
     */
    public function setOwnershipCost(int $ownership_cost): void
    {
        $this->ownership_cost = $ownership_cost;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return array[]|null
     */
    public function getParams(): ?array
    {
        return $this->params ? Json::decode($this->params) : null;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = Json::encode($params);
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
}
