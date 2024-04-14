<?php

namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс, который хранит отписавшиеся от рассылки email
 *
 * Class EmailUnsubscribers
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. email_unsubscribers
 * @property string $email                          Email
 * @property int|null $deleted_at                   Дата удаления
 * @property int $created_at                        Дата создания записи (Дата отписки)
 */
class EmailUnsubscribers extends ActiveRecord
{
    use SoftDeleteModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'email_unsubscribers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'trim'],
            ['email', 'string', 'max' => 255],
            [['created_at'], 'integer'],
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
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }

    /**
     * @param int $deleted_at
     */
    public function setDeletedAt(int $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }
}
