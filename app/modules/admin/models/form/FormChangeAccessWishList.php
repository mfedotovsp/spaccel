<?php

namespace app\modules\admin\models\form;

use app\models\Client;
use yii\base\Model;

/**
 * Форма изменения доступа к спискам запросов B2B компаний других организаций
 *
 * Class FormChangeAccessWishList
 * @package app\modules\admin\models\form
 *
 * @property bool $accessGeneralWishList                    Организация получила доступ к общим спискам запросов B2B компаний
 * @property bool $accessMyWishList                         Организация разрешает доступ к своим спискам запросов B2B компаний
 */
class FormChangeAccessWishList extends Model
{
    public $accessGeneralWishList;
    public $accessMyWishList;

    public function __construct(Client $client, $config = [])
    {
        $this->accessGeneralWishList = $client->isAccessGeneralWishList();
        $this->accessMyWishList = $client->isAccessMyWishList();
        parent::__construct($config);
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'accessGeneralWishList' => 'Организация получила доступ к общим спискам запросов B2B компаний',
            'accessMyWishList' => 'Организация разрешает доступ к своим спискам запросов B2B компаний'
        ];
    }
}