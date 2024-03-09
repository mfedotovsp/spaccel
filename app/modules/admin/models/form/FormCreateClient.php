<?php


namespace app\modules\admin\models\form;

use app\models\Client;
use yii\base\Model;

/**
 * Форма создания нового клиента (организации)
 *
 * Class FormCreateClient
 * @package app\modules\admin\models\form
 *
 * @property string $name                               наименование клиента
 * @property string $fullname                           полное наименование клиента
 * @property string $city                               город клиента
 * @property string $description                        описание клиента (подробная информация о клиенте)
 * @property string $adminCompany                       данные из формы создания администратора организации
 *
 */
class FormCreateClient extends Model
{

    public $name;
    public $fullname;
    public $city;
    public $description;
    public $adminCompany;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'fullname', 'city', 'description'], 'required'],
            ['name', 'string', 'min' => 3, 'max' => 32],
            [['fullname', 'city'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 2000],
            ['adminCompany', 'string'],
            [['name', 'fullname', 'city', 'description'], 'trim']
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Наименование организации',
            'fullname' => 'Полное наименование организации',
            'city' => 'Город организации',
            'description' => 'Описание организации'
        ];
    }


    /**
     * @return Client|null
     */
    public function create(): ?Client
    {
        $client = new Client();
        $client->setName($this->name);
        $client->setFullname($this->fullname);
        $client->setCity($this->city);
        $client->setDescription($this->description);
        return $client->save() ? $client : null;
    }

}