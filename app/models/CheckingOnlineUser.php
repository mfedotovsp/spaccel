<?php


namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс хранит информацию в бд о том, когда пользователь последний раз заходил на сайт
 *
 * Class CheckingOnlineUser
 * @package app\models
 *
 * @property int $id                            Идентификатор записи
 * @property int $user_id                       Идентификатор пользователя
 * @property int $last_active_time              Дата последнего посещения
 */
class CheckingOnlineUser extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'checking_online_user';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'last_active_time'], 'required'],
            [['user_id', 'last_active_time'], 'integer'],
        ];
    }


    /**
     * Установить текущее время и сохранить запись в бд
     */
    public function setLastActiveTime(): void
    {
        $this->last_active_time = time();
        $this->save();
    }


    /**
     * Добавить новую запись
     *
     * @param int $id
     */
    public function addCheckingOnline(int $id): void
    {
        $this->setUserId($id);
        $this->setLastActiveTime();
    }


    /**
     * @return bool|string
     */
    public function isOnline()
    {
        if ($this->last_active_time > time() - (5*60)) {
            return true;
        }
        return $this->getDateRusAndTime();
    }


    /**
     * Возвращает дату по русски + время
     *
     * @return string
     */
    public function getDateRusAndTime(): string
    {

        $monthes = array(
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
        );

        if (date('d.n.Y', $this->last_active_time) === date('d.n.Y')) {
            return 'сегодня в ' . date('H:i', $this->last_active_time);
        }
        elseif (date('d', $this->last_active_time) === (date('d') - 1)
            && date('n.Y', $this->last_active_time) === date('n.Y')) {
            return 'вчера в ' . date('H:i', $this->last_active_time);
        }
        else {

            if (date('Y', $this->last_active_time) === date('Y')) {

                return date('d', $this->last_active_time) . ' ' . $monthes[(date('n', $this->last_active_time))]
                    . ' в ' . date('H:i', $this->last_active_time);

            }

            return date('d', $this->last_active_time) . ' ' . $monthes[(date('n', $this->last_active_time))]
                    . ' ' . date(' Y', $this->last_active_time);
        }
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
    public function getLastActiveTime(): int
    {
        return $this->last_active_time;
    }
}
