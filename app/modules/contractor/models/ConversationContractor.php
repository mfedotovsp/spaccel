<?php

namespace app\modules\contractor\models;

use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс, который хранит беседы исполнителей со всеми доступными пользователями, кроме техподдержки
 *
 * Class ConversationContractor
 * @package app\modules\contractor\models
 *
 * @property int $id                                    идентификатор беседы
 * @property int $contractor_id                         идентификатор испольнителя
 * @property int $user_id                               идентификатор пользователя
 * @property int $role                                  роль пользователя
 * @property int $updated_at                            дата обновления
 *
 * @property User $contractor                           Исполнитель
 * @property User $user                                 Пользователь
 * @property MessageContractor[] $messages              Сообщения беседы
 * @property MessageContractor $lastMessage             Последнее сообщение в беседе
 * @property int $countNewMessages                      Кол-во непрочитанных сообщений в беседе
 */
class ConversationContractor extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'conversation_contractor';
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            [['id', 'contractor_id', 'user_id', 'role', 'updated_at'], 'integer'],
            ['role', 'in', 'range' => [User::ROLE_USER]],
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            //Использование поведения TimestampBehavior ActiveRecord
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],

                ],
            ],
        ];
    }


    /**
     * @return int
     */
    public function getRoleUser(): int
    {
        return $this->role;
    }


    /**
     * Получить объект пользователя
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * Получить объект исполнителя
     *
     * @return ActiveQuery
     */
    public function getContractor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
    }


    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }


    /**
     * @return int
     */
    public function getContractorId(): int
    {
        return $this->contractor_id;
    }


    /**
     * Получить все сообщения беседы
     *
     * @return ActiveQuery
     */
    public function getMessages(): ActiveQuery
    {
        return $this->hasMany(MessageContractor::class, ['conversation_id' => 'id']);
    }


    /**
     * Получить последнее сообщение беседы
     *
     * @return ActiveQuery
     */
    public function getLastMessage(): ActiveQuery
    {
        return $this->hasOne(MessageContractor::class, ['conversation_id' => 'id'])->orderBy('created_at DESC');
    }


    /**
     * Получить кол-во непрочитанных сообщений беседы
     * @return int
     */
    public function getCountNewMessages(): int
    {
        return MessageContractor::find()
            ->andWhere(['conversation_id' => $this->id, 'status' => MessageContractor::NO_READ_MESSAGE])->count();
    }


    /**
     * Проверить существует ли беседа
     * между пользователем и исполнителем
     *
     * @param int $contractorId
     * @param int $userId
     * @return bool
     */
    public static function isExist(int $contractorId, int $userId): bool
    {
        return self::find()->andWhere([
            'contractor_id' => $contractorId,
            'user_id' => $userId
        ])->exists();
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int $contractorId
     */
    public function setContractorId(int $contractorId): void
    {
        $this->contractor_id = $contractorId;
    }


    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }


    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }


    /**
     * @param int $role
     */
    public function setRole(int $role): void
    {
        $this->role = $role;
    }


    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }
}
