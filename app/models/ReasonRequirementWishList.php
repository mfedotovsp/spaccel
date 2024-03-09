<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит информацию в бд о причинах запроса компаний B2B сегмента
 *
 * Class ReasonRequirementWishList
 * @package app\models
 *
 * @property int $id                                идентификатор записи
 * @property int $requirement_wish_list_id          идентификатор запроса компании B2B сегмента
 * @property string $reason                         Описание причины
 *
 * @property RequirementWishList $requirement       Запрос
 */
class ReasonRequirementWishList extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'reason_requirement_wish_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['requirement_wish_list_id', 'reason'], 'required'],
            [['reason'], 'string', 'max' => 2000],
            [['reason'], 'trim'],
            [['requirement_wish_list_id'], 'integer'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'reason' => 'Описание причины',
        ];
    }

    /**
     * Получить запрос компаний B2B сегмента
     *
     * @return ActiveQuery
     */
    public function getRequirement(): ActiveQuery
    {
        return $this->hasOne(RequirementWishList::class, ['id' => 'requirement_wish_list_id']);
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
    public function getRequirementWishListId(): int
    {
        return $this->requirement_wish_list_id;
    }

    /**
     * @param int $requirement_wish_list_id
     */
    public function setRequirementWishListId(int $requirement_wish_list_id): void
    {
        $this->requirement_wish_list_id = $requirement_wish_list_id;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }
}