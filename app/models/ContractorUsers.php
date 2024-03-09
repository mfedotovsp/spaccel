<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит связи исполнителей с проектантами
 *
 * Class ContractorUsers
 * @package app\models
 *
 * @property int $contractor_id                 Идентификатор исполнителя
 * @property int $user_id                       Идентификатор проектанта
 *
 * @property User $contractor                   Испонитель
 * @property User $user                         Проектант
 */
class ContractorUsers extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'user_id', ], 'required'],
            [['contractor_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContractor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
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
     * @param int $contractorId
     * @param int $userId
     * @return ContractorUsers|null
     */
    public static function getInstance(int $contractorId, int $userId): ?ContractorUsers
    {
        if (!$record = self::findOne(['contractor_id' => $contractorId, 'user_id' => $userId])) {
            $newRecord = new self();
            $newRecord->setContractorId($contractorId);
            $newRecord->setUserId($userId);
            return $newRecord->save() ? $newRecord : null;
        }
        return $record;
    }
}