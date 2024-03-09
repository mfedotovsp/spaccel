<?php


namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * Класс, который хранит информацию о настройках клиентов (организациях)
 *
 * Class ClientSettings
 * @package app\models
 *
 * @property int $id                        идентификатор записи
 * @property int $client_id                 идентификатор клиента
 * @property int $admin_id                  идентификатор гл.админа организации (клиента)
 * @property string $avatar_max_image       оригинальное загруженное фото аватара клиента
 * @property string $avatar_image           урезанное (которое все видят) фото аватара клиента
 * @property int $access_admin              доступ из Spaccel к данным организации
 *
 * @property Client $client                 Организация
 * @property User $admin                    Админ организации
 */
class ClientSettings extends ActiveRecord
{

    public const ACCESS_ADMIN_TRUE = 83996983;
    public const ACCESS_ADMIN_FALSE = 1243234;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'client_settings';
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
     * Получить объект админа
     * организации (клиента)
     *
     * @return ActiveQuery
     */
    public function getAdmin(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'admin_id']);
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
    public function getAdminId(): int
    {
        return $this->admin_id;
    }


    /**
     * @param int $admin_id
     */
    public function setAdminId(int $admin_id): void
    {
        $this->admin_id = $admin_id;
    }


    /**
     * @return string|null
     */
    public function getAvatarMaxImage(): ?string
    {
        return $this->avatar_max_image;
    }


    /**
     * @param string|null $avatar_max_image
     */
    public function setAvatarMaxImage(?string $avatar_max_image = null): void
    {
        $this->avatar_max_image = $avatar_max_image;
    }


    /**
     * @return string|null
     */
    public function getAvatarImage(): ?string
    {
        return $this->avatar_image ?: '';
    }


    /**
     * @param string|null $avatar_image
     */
    public function setAvatarImage(?string $avatar_image = null): void
    {
        $this->avatar_image = $avatar_image;
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id', 'admin_id'], 'required'],
            [['client_id', 'admin_id', 'access_admin'], 'integer'],
            [['client_id', 'admin_id'], 'unique'],
            [['avatar_max_image', 'avatar_image'], 'string', 'max' => 255],
            ['access_admin', 'default', 'value' => self::ACCESS_ADMIN_FALSE],
            ['access_admin', 'in', 'range' => [
                self::ACCESS_ADMIN_FALSE,
                self::ACCESS_ADMIN_TRUE,
            ]],
        ];
    }


    /**
     * @param array $params
     * @return bool
     */
    public static function createRecord(array $params): bool
    {
        $settings = new self();
        $settings->setAttributes($params);
        if (!self::findOne(['client_id' => $settings->getClientId()])) {
            return $settings->save();
        }
        return false;
    }

    /**
     * @return int
     */
    public function getAccessAdmin(): int
    {
        return $this->access_admin;
    }

    /**
     * @param int $access_admin
     */
    public function setAccessAdmin(int $access_admin): void
    {
        $this->access_admin = $access_admin;
    }

}