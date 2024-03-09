<?php


namespace app\modules\client\models\form;

use app\models\Client;
use app\models\ClientSettings;
use yii\base\Model;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * Форма редактирования информации о клиенте (организации)
 *
 * Class FormUpdateClient
 * @package app\modules\client\models\form
 *
 * @property int $id                                    идентификатор клиента
 * @property string $name                               наименование клиента
 * @property string $fullname                           полное наименование клиента
 * @property string $city                               город клиента
 * @property string $description                        описание клиента (подробная информация о клиенте)
 * @property int $accessAdmin                           доступ из Spaccel к данным организации
 * @property Client $_client                            объект организации
 */
class FormUpdateClient extends Model
{

    public $id;
    public $name;
    public $fullname;
    public $city;
    public $description;
    public $accessAdmin;
    private $_client;


    /**
     * FormUpdateClient constructor.
     * @param Client $client
     * @param array $config
     */
    public function __construct(Client $client, array $config = [])
    {
        $this->_client = $client;
        $this->attributes = $this->_client->attributes;
        $this->accessAdmin = $client->settings->getAccessAdmin();
        parent::__construct($config);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'fullname', 'city', 'description'], 'required'],
            ['name', 'string', 'min' => 3, 'max' => 32],
            [['fullname', 'city'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 2000],
            [['name', 'fullname', 'city', 'description'], 'trim'],
            ['_client', 'safe'],
            ['accessAdmin', 'in', 'range' => [
                ClientSettings::ACCESS_ADMIN_FALSE,
                ClientSettings::ACCESS_ADMIN_TRUE,
            ]],
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
            'description' => 'Описание организации',
            'accessAdmin' => 'Доступ к данным организации'
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFullname(): string
    {
        return $this->fullname;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getAccessAdmin(): int
    {
        return $this->accessAdmin;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function update(): bool
    {
        $this->_client->attributes = $this->attributes;
        $clientSettings = $this->_client->settings;
        if ($clientSettings->getAccessAdmin() !== $this->getAccessAdmin()) {
            try {
                $clientSettings->setAccessAdmin($this->getAccessAdmin());
                $clientSettings->update();
            } catch (\Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        $this->_client->update();
        return true ;
    }

}