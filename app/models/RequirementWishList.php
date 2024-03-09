<?php

namespace app\models;

use Throwable;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Класс хранит информацию в бд о запросах компаний B2B сегмента
 *
 * Class RequirementWishList
 * @package app\models
 *
 * @property int $id                                        идентификатор записи
 * @property int $wish_list_id                              идентификатор списка запросов компаний B2B сегмента
 * @property int $is_actual                                 показатель актуальности запроса
 * @property string $requirement                            Описание запроса
 * @property string $expected_result                        Описание ожидаемого решения
 * @property string $add_info                               Дополнительная информация
 *
 * @property WishList $wishList                             Список запросов компаний B2B сегмента
 * @property ReasonRequirementWishList[] $reasons           Причины запроса
 */
class RequirementWishList extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'requirement_wish_list';
    }

    public const REQUIREMENT_ACTUAL = 5551098;
    public const REQUIREMENT_NOT_ACTUAL = 7771035;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['wish_list_id', 'requirement', 'expected_result'], 'required'],
            [['requirement', 'expected_result', 'add_info'], 'string', 'max' => 2000],
            [['requirement', 'expected_result', 'add_info'], 'trim'],
            [['wish_list_id'], 'integer'],
            ['is_actual', 'default', 'value' => self::REQUIREMENT_ACTUAL],
            ['is_actual', 'in', 'range' => [
                self::REQUIREMENT_ACTUAL,
                self::REQUIREMENT_NOT_ACTUAL,
            ]],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'requirement' => 'Описание запроса',
            'expected_result' => 'Описание ожидаемого решения',
            'add_info' => 'Дополнительная информация',
        ];
    }

    /**
     * Получить список пожеланий компаний B2B сегмента
     *
     * @return ActiveQuery
     */
    public function getWishList(): ActiveQuery
    {
        return $this->hasOne(WishList::class, ['id' => 'wish_list_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReasons(): ActiveQuery
    {
        return $this->hasMany(ReasonRequirementWishList::class, ['requirement_wish_list_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function SegmentRequirement(): ActiveQuery
    {
        return $this->hasOne(SegmentRequirement::class, ['requirement_id' => 'id']);
    }

    /**
     * @param int $id
     * @return bool|string
     */
    public function create(int $id)
    {
        try {
            $model = new self();
            $model->setWishListId($id);
            $model->setRequirement($_POST['RequirementWishList']['requirement']);
            $model->setExpectedResult($_POST['RequirementWishList']['expected_result']);
            $model->setAddInfo($_POST['RequirementWishList']['add_info']);
            if ($model->save()) {
                foreach ($_POST['RequirementWishList']['reasons'] as $reason) {
                    $newReason = new ReasonRequirementWishList();
                    $newReason->setRequirementWishListId($model->getId());
                    $newReason->setReason($reason['reason']);
                    $newReason->save();
                }
            }
            return true;
        }catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @return bool|string
     */
    public function updateRecord()
    {
        try {

            $this->setRequirement($_POST['RequirementWishList']['requirement']);
            $this->setExpectedResult($_POST['RequirementWishList']['expected_result']);
            $this->setAddInfo($_POST['RequirementWishList']['add_info']);

            if ($this->save()) {
                $query = array_values($_POST['RequirementWishList']['reasons']);
                $reasons = $this->reasons;

                if (count($query) > count($reasons)) {

                    foreach ($query as $i => $q) {

                        if (($i+1) <= count($reasons)) {
                            $reasons[$i]->setReason($q['reason']);
                        } else {
                            $reasons[$i] = new ReasonRequirementWishList();
                            $reasons[$i]->setReason($q['reason']);
                            $reasons[$i]->setRequirementWishListId($this->getId());
                        }
                        $reasons[$i]->save();
                    }

                } else {

                    foreach ($query as $i => $q) {
                        $reasons[$i]->setReason($q['reason']);
                        $reasons[$i]->setRequirementWishListId($this->getId());
                        $reasons[$i]->save();
                    }
                }
                return true;
            }
            return 'Error';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @return bool|string
     * @throws Throwable
     */
    public function deleteRecord()
    {
        try {
            $reasons = $this->reasons;
            if ($reasons) {
                foreach ($reasons as $reason) {
                    $reason->delete();
                }
            }
            $this->delete();
            return true;
        } catch (\Exception $exception) {
            return $exception->getMessage();
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
    public function getWishListId(): int
    {
        return $this->wish_list_id;
    }

    /**
     * @param int $wish_list_id
     */
    public function setWishListId(int $wish_list_id): void
    {
        $this->wish_list_id = $wish_list_id;
    }

    /**
     * @return int
     */
    public function getIsActual(): int
    {
        return $this->is_actual;
    }

    /**
     * @param int $is_actual
     */
    public function setIsActual(int $is_actual): void
    {
        $this->is_actual = $is_actual;
    }

    /**
     * @return string
     */
    public function getIsActualDesc(): string
    {
        if ($this->is_actual === self::REQUIREMENT_ACTUAL) {
            return 'Да';
        }
        return 'Нет';
    }

    /**
     * @return string
     */
    public function getRequirement(): string
    {
        return $this->requirement;
    }

    /**
     * @param string $requirement
     */
    public function setRequirement(string $requirement): void
    {
        $this->requirement = $requirement;
    }

    /**
     * @return string
     */
    public function getExpectedResult(): string
    {
        return $this->expected_result;
    }

    /**
     * @param string $expected_result
     */
    public function setExpectedResult(string $expected_result): void
    {
        $this->expected_result = $expected_result;
    }

    /**
     * @return string
     */
    public function getAddInfo(): string
    {
        return $this->add_info;
    }

    /**
     * @param string $add_info
     */
    public function setAddInfo(string $add_info): void
    {
        $this->add_info = $add_info;
    }
}