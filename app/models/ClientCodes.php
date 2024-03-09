<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит информацию о кодах клиентов (организаций)
 *
 * Class ClientCodes
 * @package app\models
 *
 * @property int $id                                                идентификатор записи
 * @property int $client_id                                         идентификатор клиента
 * @property int $type                                           тип записи (назначение)
 * @property string $code                                           код клиента
 * @property int $created_at                                        дата создания
 * @property int $updated_at                                        дата редактирования
 *
 * @property Client $client                                         Организация
 */
class ClientCodes extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'client_codes';
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
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id'], 'required'],
            [['client_id', 'type'], 'integer'],
            ['code', 'string', 'max' => 255],
            ['type', 'in', 'range' => [
                ClientCodeTypes::REGISTRATION_CODE_FOR_SIMPLE_USER,
                ClientCodeTypes::REGISTRATION_CODE_FOR_TRACKER,
                ClientCodeTypes::REGISTRATION_CODE_FOR_MANAGER,
                ClientCodeTypes::REGISTRATION_CODE_FOR_EXPERT,
                ClientCodeTypes::REGISTRATION_CODE_FOR_CONTRACTOR,
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
     * @param int $clientId
     * @param int $type
     * @return ClientCodes
     */
    public static function getInstance(int $clientId, int $type): ClientCodes
    {
        $oldClientCode = self::findOne(['client_id' => $clientId, 'type' => $type]);
        if ($oldClientCode) {
            return $oldClientCode;
        }

        $newClientCode = new self();
        $newClientCode->setClientId($clientId);
        $newClientCode->setType($type);
        return $newClientCode;
    }
}