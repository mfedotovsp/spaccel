<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс, который хранит объекты логи отправки email
 *
 * Class EmailLogs
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. email_logs
 * @property string $email                          Email получателя
 * @property string $subject                        Тема письма
 * @property string $body_html                      Текст письма
 * @property bool $is_failed                        Флаг, указывающий на то, что отправка не прошла
 * @property string $error                          Текст ощибки
 * @property int $created_at                        Дата создания записи
 */
class EmailLogs extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'email_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'subject', 'body_html'], 'required'],
            [['email', 'subject', 'body_html'], 'trim'],
            [['email', 'subject'], 'string', 'max' => 255],
            [['body_html', 'error'], 'string'],
            [['created_at'], 'integer'],
            ['is_failed', 'default', 'value' => false],
            ['is_failed', 'in', 'range' => [false, true,]],
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
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBodyHtml(): string
    {
        return $this->body_html;
    }

    /**
     * @param string $body_html
     */
    public function setBodyHtml(string $body_html): void
    {
        $this->body_html = $body_html;
    }

    /**
     * @return bool
     */
    public function isIsFailed(): bool
    {
        return $this->is_failed;
    }

    /**
     * @param bool $is_failed
     */
    public function setIsFailed(bool $is_failed): void
    {
        $this->is_failed = $is_failed;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }
}
