<?php


namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * Класс, который хранит информацию о том, в какой организации зарегистрирован пользователи
 * или какие пользователи зарегистрированы в тех или иных организациях (клиентах)
 *
 * Class ClientUser
 * @package app\models
 *
 * @property int $id                        идентификатор записи
 * @property int $user_id                   идентификатор пользователя из таблицы User
 * @property int $client_id                 идентификатор клиента (организации)
 *
 * @property Client $client                 Организация
 * @property User $user                     Пользователь
 */
class ClientUser extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'client_user';
    }


    /**
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id', 'user_id'], 'required'],
            [['client_id', 'user_id'], 'integer'],
            [['user_id'], 'unique'],
        ];
    }


    /**
     * Создание новой записи
     *
     * @param int $client_id
     * @param int $user_id
     * @return bool
     */
    public static function createRecord(int $client_id, int $user_id): bool
    {
        $model = new self();
        $model->setClientId($client_id);
        $model->setUserId($user_id);
        return $model->save();
    }
}