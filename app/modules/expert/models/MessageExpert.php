<?php

namespace app\modules\expert\models;

use app\models\MessageFiles;
use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит сообщения из бесед экспертов со всеми доступными пользователями, кроме техподдержки
 *
 * Class MessageExpert
 * @package app\modules\expert\models
 *
 * @property int $id                                            идентификатор сообщения
 * @property int $conversation_id                               идентификатор беседы
 * @property int $sender_id                                     идентификатор отправителя
 * @property int $adressee_id                                   идентификатор получателя
 * @property string $description                                текст сообщения
 * @property int $status                                        статус сообщения
 * @property int $created_at                                    дата создания
 * @property int $updated_at                                    дата обновления
 *
 * @property ConversationExpert $conversation                   Беседа
 * @property User $sender                                       Отправитель
 * @property User $adressee                                     Получатель
 * @property MessageFiles[] $files                              Прикрепленные файлы
 * @property string $dayAndDateRus                              Дата и день отправления сообщения по-русски
 * @property string $dateRus                                    Дата отправления сообщения по-русски
 */
class MessageExpert extends ActiveRecord
{

    public const READ_MESSAGE = 20;
    public const NO_READ_MESSAGE = 10;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'message_expert';
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['description'], 'filter', 'filter' => 'trim'],
            [['description'], 'string'],
            [['id', 'conversation_id','sender_id', 'adressee_id', 'status'], 'integer'],
            ['status', 'default', 'value' => function () {
                return self::NO_READ_MESSAGE;
            }],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Отправитель',
            'adressee_id' => 'Получатель',
            'status' => 'Статус прочтения',
            'description' => 'Сообщение',
            'created_at' => 'Время отправления',
        ];
    }


    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->conversation->touch('updated_at');
        });

        parent::init();
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }


    /**
     * Получить объект беседы
     *
     * @return ActiveQuery
     */
    public function getConversation(): ActiveQuery
    {
        return $this->hasOne(ConversationExpert::class, ['id' => 'conversation_id']);
    }


    /**
     * Получить объект отправителя
     *
     * @return ActiveQuery
     */
    public function getSender(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'sender_id']);
    }


    /**
     * Получить объект получателя
     * @return ActiveQuery
     */
    public function getAdressee(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'adressee_id']);
    }


    /**
     * Получить прикрепленные файлы
     *
     * @return MessageFiles[]
     */
    public function getFiles(): array
    {
        return MessageFiles::findAll(['category' => MessageFiles::CATEGORY_EXPERT, 'message_id' => $this->id]);
    }


    /**
     * Получить дату отправки сообщения
     * День и дата по-русски
     *
     * @return string
     */
    public function getDayAndDateRus(): string
    {

        $days = array(
            'Воскресенье', 'Понедельник', 'Вторник', 'Среда',
            'Четверг', 'Пятница', 'Суббота'
        );

        $monthes = array(
            1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
            5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
            9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
        );

        if (date('d.n.Y', $this->getCreatedAt()) === date('d.n.Y')) {
            return 'Сегодня';
        }

        if (date('d', $this->getCreatedAt()) === (date('d') - 1)
            && date('n.Y', $this->getCreatedAt()) === date('n.Y')) {
            return 'Вчера';
        }

        return ( $days[(date('w', $this->getCreatedAt()))] . ', ' . date('d', $this->getCreatedAt())
            . ' ' . $monthes[(date('n', $this->getCreatedAt()))] . ' ' . date(' Y', $this->getCreatedAt()));
    }


    /**
     * Получить дату отправки сообщения
     * Дата по-русски
     *
     * @return string
     */
    public function getDateRus(): string
    {

        $monthes = array(
            1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
            5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
            9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
        );

        if (date('d.n.Y', $this->getCreatedAt()) === date('d.n.Y')) {
            return 'Сегодня';
        }

        if (date('d', $this->getCreatedAt()) === (date('d') - 1)
            && date('n.Y', $this->getCreatedAt()) === date('n.Y')) {
            return 'Вчера';
        }

        return ( date('d', $this->getCreatedAt()) . $monthes[(date('n', $this->getCreatedAt()))]
            . ' ' . date(' Y', $this->getCreatedAt()));
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
    public function getConversationId(): int
    {
        return $this->conversation_id;
    }


    /**
     * @param int $conversation_id
     */
    public function setConversationId(int $conversation_id): void
    {
        $this->conversation_id = $conversation_id;
    }


    /**
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->sender_id;
    }


    /**
     * @param int $sender_id
     */
    public function setSenderId(int $sender_id): void
    {
        $this->sender_id = $sender_id;
    }


    /**
     * @return int
     */
    public function getAdresseeId(): int
    {
        return $this->adressee_id;
    }


    /**
     * @param int $adressee_id
     */
    public function setAdresseeId(int $adressee_id): void
    {
        $this->adressee_id = $adressee_id;
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }


    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
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
}
