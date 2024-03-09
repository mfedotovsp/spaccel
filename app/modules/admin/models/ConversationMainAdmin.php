<?php

namespace app\modules\admin\models;

use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс, который хранит записи о беседах админов организаций с трекерами этих организаций
 *
 * Class ConversationMainAdmin
 * @package app\modules\admin\models
 *
 * @property int $id                                        идентификатор беседы
 * @property int $main_admin_id                             идентификатор админа организации
 * @property int $admin_id                                  идентификатор трекера организации
 * @property int $updated_at                                дата обновления
 *
 * @property User $admin                                    Трекер
 * @property User $mainAdmin                                Администратор
 * @property MessageMainAdmin[] $messages                   Сообщения беседы
 * @property MessageMainAdmin $lastMessage                  Последнее сообщение в беседе
 * @property int $countNewMessages                          Кол-во непрочитанных сообщений в беседе
 */
class ConversationMainAdmin extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'conversation_main_admin';
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            [['id', 'main_admin_id', 'admin_id', 'updated_at'], 'integer'],
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
     * Получить объект трекера
     *
     * @return ActiveQuery
     */
    public function getAdmin(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'admin_id']);
    }


    /**
     * Получить объект админа организации
     *
     * @return ActiveQuery
     */
    public function getMainAdmin(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'main_admin_id']);
    }


    /**
     * Получить все сообщения беседы
     *
     * @return ActiveQuery
     */
    public function getMessages(): ActiveQuery
    {
        return $this->hasMany(MessageMainAdmin::class, ['conversation_id' => 'id']);
    }


    /**
     * Получить последнее сообщение беседы
     *
     * @return ActiveQuery
     */
    public function getLastMessage(): ActiveQuery
    {
        return $this->hasOne(MessageMainAdmin::class, ['conversation_id' => 'id'])->orderBy('created_at DESC');
    }


    /**
     * Получить кол-во непрочитанных сообщений беседы
     *
     * @return int
     */
    public function getCountNewMessages(): int
    {
        return MessageMainAdmin::find()
            ->andWhere(['conversation_id' => $this->id, 'status' => MessageMainAdmin::NO_READ_MESSAGE])->count();
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
    public function getMainAdminId(): int
    {
        return $this->main_admin_id;
    }


    /**
     * @param int $main_admin_id
     */
    public function setMainAdminId(int $main_admin_id): void
    {
        $this->main_admin_id = $main_admin_id;
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
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }
}