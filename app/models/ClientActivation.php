<?php


namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;


/**
 * Класс, который хранит информацию о активации клиентов (организациях)
 *
 * Class ClientActivation
 * @package app\models
 *
 * @property int $id                идентификатор записи
 * @property int $client_id         идентификатор клиента
 * @property int $status            состояние активации клиента
 * @property int $created_at        дата создания записи
 *
 * @property Client $client         Организация
 */
class ClientActivation extends ActiveRecord
{

    public const ACTIVE = 789;
    public const NO_ACTIVE = 987;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'client_activation';
    }


    /**
     * Получить объект клиента
     *
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }


    /**
     * Получить текущий статус клиента
     *
     * @param $clientId
     * @return int
     */
    public static function getCurrentStatus($clientId): int
    {
        $obj = self::find()->andWhere(['client_id' => $clientId])->orderBy(['id' => SORT_DESC])->one();
        return $obj->getStatus();
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
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id'], 'required'],
            [['created_at', 'client_id', 'status'], 'integer'],
            ['status', 'default', 'value' => function () {
                return self::NO_ACTIVE;
            }],
            ['status', 'in', 'range' => [
                self::NO_ACTIVE,
                self::ACTIVE
            ]],
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


}