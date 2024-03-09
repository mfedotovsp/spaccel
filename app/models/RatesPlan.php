<?php


namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;


/**
 * Класс хранит общие сведения о тарифных планах
 *
 * Class RatesPlan
 * @package app\models
 *
 * @property int $id                            индентификатор тарифного плана
 * @property string $name                       наименование тарифного плана
 * @property string $description                описание тарифного плана
 * @property int $max_count_project_user        максимальное количество проектантов по тарифному плану
 * @property int $max_count_tracker             максимальное количество трекеров по тарифному плану
 * @property int $created_at                    дата создания тарифного плана
 *
 * @property ClientRatesPlan[] $clientRatesPlans
 */
class RatesPlan extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'rates_plan';
    }


    /**
     * @return ActiveQuery
     */
    public function getClientRatesPlans(): ActiveQuery
    {
        return $this->hasMany(ClientRatesPlan::class, ['rates_plan_id' => 'id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'description', 'max_count_project_user', 'max_count_tracker'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 2000],
            [['max_count_project_user', 'max_count_tracker', 'created_at'], 'integer'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Наименование',
            'description' => 'Описание',
            'max_count_project_user' => 'Максимальное количество проектантов',
            'max_count_tracker' => 'Максимальное количество трекеров',
            'created_at' => 'Дата создания',
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    public function getMaxCountProjectUser(): int
    {
        return $this->max_count_project_user;
    }


    /**
     * @param int $max_count_project_user
     */
    public function setMaxCountProjectUser(int $max_count_project_user): void
    {
        $this->max_count_project_user = $max_count_project_user;
    }


    /**
     * @return int
     */
    public function getMaxCountTracker(): int
    {
        return $this->max_count_tracker;
    }


    /**
     * @param int $max_count_tracker
     */
    public function setMaxCountTracker(int $max_count_tracker): void
    {
        $this->max_count_tracker = $max_count_tracker;
    }


    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }


}