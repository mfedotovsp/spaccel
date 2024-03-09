<?php

namespace app\modules\expert\models;

use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс, который хранит беседы экспертов со всеми доступными пользователями, кроме техподдержки
 *
 * Class ConversationExpert
 * @package app\modules\expert\models
 *
 * @property int $id                                    идентификатор беседы
 * @property int $expert_id                             идентификатор эксперта
 * @property int $user_id                               идентификатор пользователя
 * @property int $role                                  роль пользователя
 * @property int $updated_at                            дата обновления
 *
 * @property User $expert                               Эксперт
 * @property User $user                                 Пользователь
 * @property MessageExpert[] $messages                  Сообщения беседы
 * @property MessageExpert $lastMessage                 Последнее сообщение в беседе
 * @property int $countNewMessages                      Кол-во непрочитанных сообщений в беседе
 */
class ConversationExpert extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'conversation_expert';
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            [['id', 'expert_id', 'user_id', 'role', 'updated_at'], 'integer'],
            ['role', 'in', 'range' => [
                User::ROLE_USER,
                User::ROLE_ADMIN,
                User::ROLE_MAIN_ADMIN,
                User::ROLE_ADMIN_COMPANY,
                User::ROLE_MANAGER
            ]],
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
     * Получить объект эксперта
     *
     * @return ActiveQuery
     */
    public function getExpert(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'expert_id']);
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
    public function getExpertId(): int
    {
        return $this->expert_id;
    }


    /**
     * Получить все сообщения беседы
     *
     * @return ActiveQuery
     */
    public function getMessages(): ActiveQuery
    {
        return $this->hasMany(MessageExpert::class, ['conversation_id' => 'id']);
    }


    /**
     * Получить последнее сообщение беседы
     *
     * @return ActiveQuery
     */
    public function getLastMessage(): ActiveQuery
    {
        return $this->hasOne(MessageExpert::class, ['conversation_id' => 'id'])->orderBy('created_at DESC');
    }


    /**
     * Получить кол-во непрочитанных сообщений беседы
     * @return int
     */
    public function getCountNewMessages(): int
    {
        return MessageExpert::find()
            ->andWhere(['conversation_id' => $this->id, 'status' => MessageExpert::NO_READ_MESSAGE])->count();
    }


    /**
     * Проверить существует ли беседа
     * между пользователем и экспертом
     *
     * @param int $expert_id
     * @param int $user_id
     * @return bool
     */
    public static function isExist(int $expert_id, int $user_id): bool
    {
        $conversation = self::findOne([
            'expert_id' => $expert_id,
            'user_id' => $user_id
        ]);

        return (bool)$conversation;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int $expert_id
     */
    public function setExpertId(int $expert_id): void
    {
        $this->expert_id = $expert_id;
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