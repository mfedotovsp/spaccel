<?php

namespace app\models\forms;

use yii\base\Model;

class SearchForm extends Model
{

    public $search;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['search'], 'trim'],
            [['search'], 'string', 'min' => 5, 'max' => 255],
        ];
    }
}