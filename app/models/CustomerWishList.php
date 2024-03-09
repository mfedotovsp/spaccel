<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс хранит информацию в бд о доступах организаций к спискам запросов компаний B2B сегмента
 *
 * Class CustomerWishList
 * @package app\models
 *
 * @property int $id                        идентификатор записи
 * @property int $client_id                 идентификатор организации, к записям которой будет открыт доступ
 * @property int $customer_id               идентификатор организации, которая получает доступ
 * @property int $created_at                дата создания
 * @property int|null $deleted_at           дата удаления
 *
 * @property Client $client                 Организация, к записям которой будет открыт доступ
 * @property Client $customer               Организация, которая получает доступ
 */
class CustomerWishList extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'customer_wish_list';
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
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id', 'customer_id'], 'required'],
            [['client_id', 'customer_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * Получить объект организации
     *
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    /**
     * Получить объект организации
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'customer_id ']);
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
    public function getCustomerId(): int
    {
        return $this->customer_id;
    }

    /**
     * @param int $customer_id
     */
    public function setCustomerId(int $customer_id): void
    {
        $this->customer_id = $customer_id;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
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