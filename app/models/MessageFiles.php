<?php


namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс хранит информацию в бд о прикрепленных к сообщениям файлах
 *
 * Class MessageFiles
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. message_files
 * @property int $message_id                        Идентификатор сообщения
 * @property int $category                          Категория беседы, к которой относится сообщение
 * @property string $file_name                      Имя файла, которое передал пользователь
 * @property string $server_file                    Сгенерированное имя файла на сервере
 */
class MessageFiles extends ActiveRecord
{

    public const CATEGORY_ADMIN = 1;
    public const CATEGORY_MAIN_ADMIN = 2;
    public const CATEGORY_TECHNICAL_SUPPORT = 3;
    public const CATEGORY_EXPERT = 4;
    public const CATEGORY_MANAGER = 5;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'message_files';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['category', 'message_id'], 'integer'],
            [['file_name', 'server_file'], 'string', 'max' => 255],
            ['category', 'in', 'range' => [
                self::CATEGORY_ADMIN,
                self::CATEGORY_MAIN_ADMIN,
                self::CATEGORY_TECHNICAL_SUPPORT,
                self::CATEGORY_EXPERT,
                self::CATEGORY_MANAGER,
            ]],
        ];
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
    public function getMessageId(): int
    {
        return $this->message_id;
    }

    /**
     * @param int $message_id
     */
    public function setMessageId(int $message_id): void
    {
        $this->message_id = $message_id;
    }

    /**
     * @return int
     */
    public function getCategory(): int
    {
        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory(int $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->file_name;
    }

    /**
     * @param string $file_name
     */
    public function setFileName(string $file_name): void
    {
        $this->file_name = $file_name;
    }

    /**
     * @return string
     */
    public function getServerFile(): string
    {
        return $this->server_file;
    }

    /**
     * @param string $server_file
     */
    public function setServerFile(string $server_file): void
    {
        $this->server_file = $server_file;
    }
}