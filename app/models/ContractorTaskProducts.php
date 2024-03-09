<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который содержит информацию о продуктах представленных
 * для решения проблемы(удовлетворения потребности),
 * которые создал исполнитель(маркетолог) в своем отчете
 *
 * Class ContractorTaskHistory
 * @package app\models
 *
 * @property int $id                            Идентификатор записи
 * @property int $contractor_id                 Идентификатор исполнителя
 * @property int $task_id                       Идентификатор задачи
 * @property string $name                       Наименование продукта
 * @property int $price                         Цена продукта
 * @property int $satisfaction                  Удовлетворенность продуктом
 * @property string $flaws                      Недостатки продукта
 * @property string $advantages                 Преимущества продукта
 * @property string $suppliers                  Ключевые поставщики
 * @property int $created_at                    Дата создания
 * @property int $updated_at                    Дата редактирования
 *
 * @property ContractorTasks $task              Объект задачи
 * @property User $contractor                   Объект исполнителя
 */
class ContractorTaskProducts extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_task_products';
    }

    public const SATISFACTION_LOW = 9871;
    public const SATISFACTION_MIDDLE = 4534;
    public const SATISFACTION_HIGH = 3476;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'task_id', 'name', 'price', 'satisfaction', 'flaws', 'advantages', 'suppliers'], 'required'],
            [['contractor_id', 'task_id', 'created_at', 'updated_at'], 'integer'],
            ['price', 'integer', 'min' => 1, 'max' => 1000000000],
            ['satisfaction', 'in', 'range' => [
                self::SATISFACTION_LOW,
                self::SATISFACTION_MIDDLE,
                self::SATISFACTION_HIGH
            ]],
            [['name'], 'string', 'max' => 255],
            [['flaws', 'advantages', 'suppliers'], 'string', 'max' => '500'],
            [['name', 'flaws', 'advantages', 'suppliers'], 'trim'],
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
            'satisfaction' => 'Удовлетворенность продуктом',
            'flaws' => 'Недостатки продукта',
            'advantages' => 'Преимущества продукта',
            'suppliers' => 'Ключевые поставщики',
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
     * @return string
     */
    public function getTitleSatisfaction(): string
    {
        switch ($this->satisfaction) {
            case self::SATISFACTION_LOW:
                return 'Низкая';
            case self::SATISFACTION_MIDDLE:
                return 'Средняя';
            case self::SATISFACTION_HIGH:
                return 'Высокая';
            default:
                return '';
        }
    }

    /**
     * @return int
     */
    public function getSatisfaction(): int
    {
        return $this->satisfaction;
    }

    /**
     * @param int $satisfaction
     */
    public function setSatisfaction(int $satisfaction): void
    {
        $this->satisfaction = $satisfaction;
    }

    /**
     * @return string
     */
    public function getFlaws(): string
    {
        return nl2br($this->flaws);
    }

    /**
     * @param string $flaws
     */
    public function setFlaws(string $flaws): void
    {
        $this->flaws = $flaws;
    }

    /**
     * @return string
     */
    public function getAdvantages(): string
    {
        return nl2br($this->advantages);
    }

    /**
     * @param string $advantages
     */
    public function setAdvantages(string $advantages): void
    {
        $this->advantages = $advantages;
    }

    /**
     * @return string
     */
    public function getSuppliers(): string
    {
        return nl2br($this->suppliers);
    }

    /**
     * @param string $suppliers
     */
    public function setSuppliers(string $suppliers): void
    {
        $this->suppliers = $suppliers;
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