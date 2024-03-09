<?php


namespace app\modules\expert\models\form;

use yii\base\Model;

class SearchForm extends Model
{

    public $search;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['search'], 'trim'],
        ];
    }
}