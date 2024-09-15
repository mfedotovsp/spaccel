<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит информацию о вариантах проблем
 * к описанию подтверждения сегмента для учебного варианта
 *
 * Class ProblemVariant
 * @package app\models
 *
 * @property int $id                                        Идентификатор записи
 * @property int $description_id                            Идентификатор в таб. confirm_descriptions
 * @property int $confirm_id                                Идентификатор в таб. confirm_segment
 * @property string $description                            Описание проблемы
 *
 * @property ConfirmDescription $confirmDescription         Описание подтверждения сегмента
 * @property ConfirmSegment $confirmSegment                 Подтверждение сегмента
 */
class ProblemVariant extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'problem_variants';
    }


    /**
     * Получить объект описания подтверждения сегмента
     *
     * @return ActiveQuery
     */
    public function getConfirmDescription(): ActiveQuery
    {
        return $this->hasOne(ConfirmDescription::class, ['id' => 'description_id']);
    }

    /**
     * Получить объект подтверждения сегмента
     *
     * @return ActiveQuery
     */
    public function getConfirmSegment(): ActiveQuery
    {
        return $this->hasOne(ConfirmSegment::class, ['id' => 'confirm_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['description_id', 'confirm_id', 'description'], 'required'],
            [['description_id', 'confirm_id'], 'integer'],
            ['description', 'string', 'max' => 2000],
            ['description', 'trim'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Описание проблемы',
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
     */
    public function setDescriptionId(int $description_id): void
    {
        $this->description_id = $description_id;
    }

    /**
     * @return int
     */
    public function getConfirmId(): int
    {
        return $this->confirm_id;
    }

    /**
     * @param int $confirm_id
     */
    public function setConfirmId(int $confirm_id): void
    {
        $this->confirm_id = $confirm_id;
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
     * @return $this
     */
    public function setDescription(string $description): ProblemVariant
    {
        $this->description = $description;
        return $this;
    }


}
