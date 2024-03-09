<?php

namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит связь сегмента и выбранного для сегмента B2B запроса компании из виш-листа
 *
 * Class SegmentRequirement
 * @package app\models
 *
 * @property int $segment_id                Идентификатор сегмента
 * @property int $requirement_id            Идентификатор запроса
 * @property int|null $deleted_at           Дата удаления
 *
 * @property Segments $segment
 * @property RequirementWishList $requirement
 */
class SegmentRequirement extends ActiveRecord
{
    use SoftDeleteModelTrait;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'segment_requirement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['segment_id', 'requirement_id'], 'required'],
            [['segment_id', 'requirement_id'], 'integer']
        ];
    }

    /**
     * @param int $segment_id
     * @param int $requirement_id
     * @return bool
     */
    public static function create(int $segment_id, int $requirement_id): bool
    {
        $model = new self();
        $model->setSegmentId($segment_id);
        $model->setRequirementId($requirement_id);
        return $model->save();
    }

    /**
     * @return ActiveQuery
     */
    public function getSegment(): ActiveQuery
    {
        return $this->hasOne(Segments::class, ['id' => 'segment_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRequirement(): ActiveQuery
    {
        return $this->hasOne(RequirementWishList::class, ['id' => 'requirement_id']);
    }

    /**
     * @return int
     */
    public function getSegmentId(): int
    {
        return $this->segment_id;
    }

    /**
     * @param int $segment_id
     */
    public function setSegmentId(int $segment_id): void
    {
        $this->segment_id = $segment_id;
    }

    /**
     * @return int
     */
    public function getRequirementId(): int
    {
        return $this->requirement_id;
    }

    /**
     * @param int $requirement_id
     */
    public function setRequirementId(int $requirement_id): void
    {
        $this->requirement_id = $requirement_id;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }
}