<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит объекты описания подтверждений для учебного варианта
 *
 * Class ConfirmDescription
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. confirm_descriptions
 * @property int $confirm_id                        Идентификатор записи в таб. confirm_*
 * @property int $hypothesis_id                     Идентификатор гипотезы
 * @property int $type                              Тип гипотезы
 * @property string $description                    Описание
 * @property int $created_at                        Дата создания
 * @property int $updated_at                        Дата обновления
 *
 * @property ProblemVariant[] $problemVariants      Варианты проблем
 * @property ConfirmSource[] $confirmSources        Источники информации
 *
 * @property ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp        $confirm   Подтверждение гипотезы
 */
class ConfirmDescription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'confirm_descriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['confirm_id', 'hypothesis_id', 'type', 'description'], 'required'],
            [['confirm_id', 'hypothesis_id', 'type'], 'integer'],
            ['description', 'string', 'max' => 5000],
            ['description', 'trim'],
            ['type', 'in', 'range' => [
                StageExpertise::CONFIRM_SEGMENT,
                StageExpertise::CONFIRM_PROBLEM,
                StageExpertise::CONFIRM_GCP,
                StageExpertise::CONFIRM_MVP,
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
     * Варианты проблем
     *
     * @return ActiveQuery
     */
    public function getProblemVariants(): ActiveQuery
    {
        return $this->hasMany(ProblemVariant::class, ['description_id' => 'id']);
    }

    /**
     * Источники информации
     *
     * @return ActiveQuery
     */
    public function getConfirmSources(): ActiveQuery
    {
        return $this->hasMany(ConfirmSource::class, ['description_id' => 'id']);
    }

    /**
     * Подтверждение гипотезы
     *
     * @return ActiveQuery|null
     */
    public function getConfirm(): ?ActiveQuery
    {
        switch ($this->getType()) {
            case StageExpertise::CONFIRM_SEGMENT:
                $confirmClass = ConfirmSegment::class; break;
            case StageExpertise::CONFIRM_PROBLEM:
                $confirmClass = ConfirmProblem::class; break;
            case StageExpertise::CONFIRM_GCP:
                $confirmClass = ConfirmGcp::class; break;
            case StageExpertise::CONFIRM_MVP:
                $confirmClass = ConfirmMvp::class; break;
            default:
                $confirmClass = null;
        }

        if (!$confirmClass) {
            return null;
        }

        return $this->hasOne($confirmClass, ['id' => 'confirm_id']);
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
     * @return int
     */
    public function getHypothesisId(): int
    {
        return $this->hypothesis_id;
    }

    /**
     * @param int $hypothesis_id
     */
    public function setHypothesisId(int $hypothesis_id): void
    {
        $this->hypothesis_id = $hypothesis_id;
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
     */
    public function setType(int $type): void
    {
        $this->type = $type;
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
