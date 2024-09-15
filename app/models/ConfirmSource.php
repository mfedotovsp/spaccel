<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит объекты источников описания подтверждений для учебного варианта
 *
 * Class ConfirmSource
 * @package app\models
 *
 * @property int $id                                        Идентификатор записи в таб. confirm_sources
 * @property int $description_id                            Идентификатор записи в таб. confirm_descriptions
 * @property int $type                                      Тип источника
 * @property string $comment                                Комментарий
 * @property int $created_at                                Дата создания
 * @property int $updated_at                                Дата обновления
 *
 * @property ConfirmDescription $confirmDescription         Описание подтверждения
 * @property ConfirmFile[] $files                           Файлы
 */
class ConfirmSource extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'confirm_sources';
    }

    public const DESC_TYPE_MARKETING = 'Экспертные исследования, полученные от маркетолога';
    public const DESC_TYPE_COMPANY = 'Экспертные исследования, полученные от компании';
    public const DESC_TYPE_PUBLIC_DATA = 'Экспертные исследования, полученные из открытых источников (обзоров)';
    public const DESC_TYPE_OTHER = 'Иное';

    public const TYPE_MARKETING = 99434;
    public const TYPE_COMPANY = 55225;
    public const TYPE_PUBLIC_DATA = 86335;
    public const TYPE_OTHER = 20564;

    public $checked = false;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['description_id', 'type', 'comment'], 'required'],
            [['description_id', 'type'], 'integer'],
            ['comment', 'string', 'max' => 2000],
            ['type', 'in', 'range' => [
                self::TYPE_MARKETING,
                self::TYPE_COMPANY,
                self::TYPE_PUBLIC_DATA,
                self::TYPE_OTHER,
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Описание',
        ];
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
     * Получить объект описания подтверждения
     *
     * @return ActiveQuery
     */
    public function getConfirmDescription(): ActiveQuery
    {
        return $this->hasOne(ConfirmDescription::class, ['id' => 'description_id']);
    }

    /**
     * Файлы приложенные к источнику
     * информации подтверждения гипотезы
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(ConfirmFile::class, ['source_id' => 'id']);
    }

    /**
     * @return string[]
     */
    public static function dataSelect(): array
    {
        return [
            self::TYPE_MARKETING => self::DESC_TYPE_MARKETING,
            self::TYPE_COMPANY => self::DESC_TYPE_COMPANY,
            self::TYPE_PUBLIC_DATA => self::DESC_TYPE_PUBLIC_DATA,
            self::TYPE_OTHER => self::DESC_TYPE_OTHER,
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
    public function getDescriptionId(): int
    {
        return $this->description_id;
    }

    /**
     * @param int $description_id
     * @return $this
     */
    public function setDescriptionId(int $description_id): ConfirmSource
    {
        $this->description_id = $description_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type): ConfirmSource
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment(string $comment): ConfirmSource
    {
        $this->comment = $comment;
        return $this;
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
