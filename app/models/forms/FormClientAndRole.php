<?php


namespace app\models\forms;

use yii\base\Model;

/**
 * Форма указания организации и роли при регистрации
 *
 * Class FormClientAndRole
 * @package app\models\forms
 *
 * @property int $clientId
 * @property int $role
 */
class FormClientAndRole extends Model
{

    public $clientId;
    public $role;

    /**
     * @param int $clientId
     * @return void
     */
    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $role
     * @return void
     */
    public function setRole(int $role): void
    {
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'clientId' => 'Организация, к которой будет привязан Ваш аккаунт *',
            'role' => 'Проектная роль пользователя *',
        ];
    }
}