<?php


namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс, который хранит беседы между трекерами и проетантами
 *
 * Class ConversationAdmin
 * @package app\models
 *
 * @property int $id                                    идентификатор беседы
 * @property int $admin_id                              идентификатор трекера
 * @property int $user_id                               идентификатор проектанта
 * @property int $updated_at                            дата обновления
 *
 * @property User $admin                                Трекер
 * @property User $user                                 Пользователь
 * @property MessageAdmin[] $messages                   Сообщения беседы
 * @property MessageAdmin $lastMessage                  Последнее сообщение в беседе
 * @property int $countNewMessages                      Кол-во непрочитанных сообщений в беседе
 */
class ConversationAdmin extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'conversation_admin';
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'admin_id', 'user_id', 'updated_at'], 'integer'],
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
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],

                ],
            ],
        ];
    }


    /**
     * Получить объект Трекера
     *
     * @return ActiveQuery
     */
    public function getAdmin(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'admin_id']);
    }


    /**
     * Получить объект проектанта
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * Получить все сообщения беседы
     *
     * @return ActiveQuery
     */
    public function getMessages(): ActiveQuery
    {
        return $this->hasMany(MessageAdmin::class, ['conversation_id' => 'id']);
    }


    /**
     * Получить последнее сообщение беседы
     *
     * @return ActiveQuery
     */
    public function getLastMessage(): ActiveQuery
    {
        return $this->hasOne(MessageAdmin::class, ['conversation_id' => 'id'])->orderBy('created_at DESC');
    }


    /**
     * Получить кол-во непрочитанных
     * сообщений беседы
     *
     * @return int|string
     */
    public function getCountNewMessages()
    {
        return MessageAdmin::find()
            ->andWhere(['conversation_id' => $this->id, 'status' => MessageAdmin::NO_READ_MESSAGE])->count();
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
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }
}