<?php


namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;


/**
 * Класс хранит сведения о том на какие тарифные планы подключены клиенты(организации),
 * в том числе и период в который будет действовать тариф у конкретного клиента.
 *
 * Class ClientRatesPlan
 * @package app\models
 *
 * @property int $id                    идентификатор в таблице client_rates_plan
 * @property int $client_id             идентификатор клиента(организации) подключенного на тариф
 * @property int $rates_plan_id         идентификатор тарифного плана
 * @property int $date_start            дата начала действия тарифа у клиента
 * @property int $date_end              дата окончания действия тарифа у клиента
 * @property int $created_at            дата создания записи в таблице client_rates_plan
 *
 * @property RatesPlan $ratesPlan       Тарифный план
 * @property Client $client             Организация
 */
class ClientRatesPlan extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'client_rates_plan';
    }


    /**
     * @return ActiveQuery
     */
    public function getRatesPlan(): ActiveQuery
    {
        return $this->hasOne(RatesPlan::class, ['id' => 'rates_plan_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id', 'rates_plan_id', 'date_start', 'date_end'], 'required'],
            [['client_id', 'rates_plan_id', 'date_start', 'date_end'], 'integer'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'date_start' => 'Начало действия тарифа',
            'date_end' => 'Окончание действия тарифа',
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
     * @return int
     */
    public function getClientId(): int
    {
        return $this->client_id;
    }


    /**
     * @param int $client_id
     */
    public function setClientId(int $client_id): void
    {
        $this->client_id = $client_id;
    }


    /**
     * @return int
     */
    public function getRatesPlanId(): int
    {
        return $this->rates_plan_id;
    }


    /**
     * @param int $rates_plan_id
     */
    public function setRatesPlanId(int $rates_plan_id): void
    {
        $this->rates_plan_id = $rates_plan_id;
    }


    /**
     * @return int|null
     */
    public function getDateStart(): ?int
    {
        return $this->date_start;
    }


    /**
     * @param int $date_start
     */
    public function setDateStart(int $date_start): void
    {
        $this->date_start = $date_start;
    }


    /**
     * @return int|null
     */
    public function getDateEnd(): ?int
    {
        return $this->date_end;
    }


    /**
     * @param int $date_end
     */
    public function setDateEnd(int $date_end): void
    {
        $this->date_end = $date_end;
    }


    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }
}