<?php


namespace app\models;

use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Класс для сортивки сегментов проекта по заданным параметрам
 *
 * Class SegmentSort
 * @package app\models
 */
class SegmentSort extends Model
{

    public static $array = [

        0 => ['id' => 1, 'parent_id' => 0, 'name' => 'по последовательности'],
        1 => ['id' => 2, 'parent_id' => 0, 'name' => 'по наличию подтверждения'],
        2 => ['id' => 3, 'parent_id' => 0, 'name' => 'по наименованию'],
        3 => ['id' => 4, 'parent_id' => 0, 'name' => 'по типу'],
        4 => ['id' => 5, 'parent_id' => 0, 'name' => 'по сфере деятельности'],
        5 => ['id' => 6, 'parent_id' => 0, 'name' => 'по виду / специализации деятельности'],
        6 => ['id' => 7, 'parent_id' => 0, 'name' => 'по платежеспособности'],
        7 => ['id' => 8, 'parent_id' => 1, 'name' => 'сначала старые', 'type_sort' => ['created_at' => SORT_ASC]],
        8 => ['id' => 9, 'parent_id' => 1, 'name' => 'сначала новые', 'type_sort' => ['created_at' => SORT_DESC]],
        9 => ['id' => 10, 'parent_id' => 2, 'name' => 'сначала ожидающие подтверждения', 'type_sort' => ['exist_confirm' => SORT_ASC]],
        10 => ['id' => 11, 'parent_id' => 2, 'name' => 'сначала подтвержденные', 'type_sort' => ['exist_confirm' => SORT_DESC]],
        11 => ['id' => 12, 'parent_id' => 2, 'name' => 'сначала неподтвержденные', 'type_sort' => ['exist_confirm' => SORT_ASC]],
        12 => ['id' => 13, 'parent_id' => 3, 'name' => 'по алфавиту - от а до я', 'type_sort' => ['name' => SORT_ASC]],
        13 => ['id' => 14, 'parent_id' => 3, 'name' => 'по алфавиту - от я до а', 'type_sort' => ['name' => SORT_DESC]],
        14 => ['id' => 15, 'parent_id' => 4, 'name' => 'сначала по типу B2C', 'type_sort' => ['type_of_interaction_between_subjects' => SORT_ASC]],
        15 => ['id' => 16, 'parent_id' => 4, 'name' => 'сначала по типу B2B', 'type_sort' => ['type_of_interaction_between_subjects' => SORT_DESC]],
        16 => ['id' => 17, 'parent_id' => 5, 'name' => 'по алфавиту - от а до я', 'type_sort' => ['field_of_activity' => SORT_ASC]],
        17 => ['id' => 18, 'parent_id' => 5, 'name' => 'по алфавиту - от я до а', 'type_sort' => ['field_of_activity' => SORT_DESC]],
        18 => ['id' => 19, 'parent_id' => 6, 'name' => 'по алфавиту - от а до я', 'type_sort' => ['sort_of_activity' => SORT_ASC]],
        19 => ['id' => 20, 'parent_id' => 6, 'name' => 'по алфавиту - от я до а', 'type_sort' => ['sort_of_activity' => SORT_DESC]],
        20 => ['id' => 21, 'parent_id' => 7, 'name' => 'по возрастанию', 'type_sort' => ['market_volume' => SORT_ASC]],
        21 => ['id' => 22, 'parent_id' => 7, 'name' => 'по убыванию', 'type_sort' => ['market_volume' => SORT_DESC]],

    ];


    /**
     * @return array
     */
    public static function getListFields(): array
    {
        $listFields = self::$array;

        foreach ($listFields as $key => $field) {

            if ($field['parent_id'] !== 0) {

                unset($listFields[$key]);
            }
        }

        return $listFields;
    }


    /**
     * @param int $area_id
     * @return array
     */
    public static function getListTypes(int $area_id): array
    {
        $listTypes = self::$array;

        foreach ($listTypes as $key => $type) {

            if ($type['parent_id'] !== $area_id) {

                unset($listTypes[$key]);
            }
        }

        return $listTypes;
    }


    /**
     * @param int $project_id
     * @param int $type_sort_id
     * @return array|ActiveRecord[]
     */
    public function fetchModels (int $project_id, int $type_sort_id): array
    {
        $array_sort = self::$array;

        $key_arr = array_search($type_sort_id, array_column($array_sort, 'id'), false);

        $search_type_sort = $array_sort[$key_arr]['type_sort'];

        if ($type_sort_id === 12){
            // Для того чтобы вывести значения в порядке [0, 1, null]
            $models_not_null = Segments::find()->andWhere(['project_id' => $project_id])->andWhere(['is not', 'exist_confirm', null])->orderBy($search_type_sort)->all();
            $models_is_null = Segments::find()->andWhere(['project_id' => $project_id])->andWhere(['is', 'exist_confirm', null])->orderBy($search_type_sort)->all();
            return array_merge($models_not_null, $models_is_null);
        }

        return Segments::find()->andWhere(['project_id' => $project_id])->orderBy($search_type_sort)->all();
    }

}