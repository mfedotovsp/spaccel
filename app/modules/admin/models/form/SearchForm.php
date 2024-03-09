<?php

namespace app\modules\admin\models\form;

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
        ];
    }
}