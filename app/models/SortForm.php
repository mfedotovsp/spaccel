<?php


namespace app\models;

use yii\base\Model;

/**
 * Форма для сортировки переданных данных
 *
 * Class SortForm
 * @package app\models
 *
 * @property string $field
 * @property string $type
 * @property int $limit
 */
class SortForm extends Model
{

    public $field;
    public $type;
    public $limit;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['field', 'type'], 'trim'],
            ['limit', 'integer'],
        ];
    }
}