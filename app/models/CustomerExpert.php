<?php


namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * Класс, который хранит информацию о том, к какому клиенту (организации) какой привязан эксперт от платформы spaccel.ru
 *
 * Class CustomerExpert
 * @package app\models
 *
 * @property int $id                        идентификатор записи
 * @property int $user_id                   идентификатор эксперта из таблицы User
 * @property int $client_id                 идентификатор клиента (организации)
 * @property int $status                    статус эксперта по данному клиенту
 * @property int $created_at                дата привязки эксперта по клиентам к организации
 * @property int $updated_at                дата изменения статуса эксперта по клиентам к организации
 *
 * @property User $user                     Эксперт
 * @property Client $client                 Организация
 */
class CustomerExpert extends ActiveRecord
{

    public const ACTIVE = 9378;
    public const NO_ACTIVE = 8351;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'customer_expert';
    }


    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
            [['client_id', 'user_id'], 'required'],
            [['client_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
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
            TimestampBehavior::class,
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
    public function getUserId(): int
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
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
}