<?php


namespace app\models;

use app\modules\admin\models\ConversationManager;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;


/**
 * Класс, который хранит информацию о том, к какому клиенту (организации) какой привязан менеджер по клиентам от платформы spaccel.ru
 *
 * Class CustomerManager
 * @package app\models
 *
 * @property int $id                        идентификатор записи
 * @property int $user_id                   идентификатор менеджера из таблицы User
 * @property int $client_id                 идентификатор клиента (организации)
 * @property int $created_at                дата привязки менеджера по клиентам к организации
 *
 * @property User $user                     Менеджер
 * @property Client $client                 Организация
 */
class CustomerManager  extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'customer_manager';
    }


    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }


    /**
     * Создание новой записи при назначении менеджера клиенту
     *
     * @param int $clientId
     * @param int $userId
     * @return CustomerManager|null
     */
    public static function addManager(int $clientId, int $userId): ?CustomerManager
    {
        $model = new self();
        $model->setClientId($clientId);
        $model->setUserId($userId);
        return $model->save() ? $model : null;
    }


    /**
     * Создать или передать существующую беседу
     * менеджера Spaccel с админом компании
     *
     * @return void
     */
    private function createConversationManagerWithAdminCompany(): void
    {
        $client = Client::findOne($this->getClientId());
        $adminCompany = User::findOne($client->settings->getAdminId());
        $conversationManager = new ConversationManager();
        $conversationManager->createOrUpdateRecordWithAdminCompany($this->getUserId(), $adminCompany);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id', 'user_id'], 'required'],
            [['client_id', 'user_id', 'created_at'], 'integer'],
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
        ];
    }


    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->createConversationManagerWithAdminCompany();
        });

        parent::init();
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
    public function getUserId(): int
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }


    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->client_id;
    }


    /**
     * @param int $client_id
     */
    public function setClientId(int $client_id): void
    {
        $this->client_id = $client_id;
    }

}