<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс, который хранит виды деятельности исполнителей проектов
 *
 * Class ContractorActivities
 * @package app\models
 *
 * @property int $id                        Идентификатор вида деятельности
 * @property string $title                  Название вида деятельности
 */
class ContractorActivities extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_activities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title'], 'required'],
            ['title', 'string', 'max' => 255],
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
    public function getTitle(): string
    {
        return $this->title;
    }
}