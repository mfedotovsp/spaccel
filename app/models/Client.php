<?php


namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * Класс, который хранит информацию о клиентах (организациях)
 *
 * Class Client
 * @package app\models
 *
 * @property int $id                                                идентификатор клиента
 * @property string $name                                           наименование клиента
 * @property string $fullname                                       полное наименование клиента
 * @property string $city                                           город клиента
 * @property string $description                                    описание клиента
 * @property int $created_at                                        дата регистраниции клиента
 * @property int $updated_at                                        дата редактирования клиента
 *
 * @property ClientActivation[] $clientActivationRecords            Все записи по клиенту в таблице client_activation
 * @property ClientSettings $settings                               Настройки организации
 * @property ClientRatesPlan[] $clientRatesPlans                    Тарифные планы, назначаемые организации
 * @property ClientUser[] $clientUsers                              Пользователи, привязанные к организации
 * @property CustomerManager[] $customerManagers                    Менеджеры Spaccel, которые когда-либо были привязаны к организации
 * @property CustomerTracker[] $customerTrackers                    Трекеры Spaccel, которые когда-либо были привязаны к организации
 * @property int $countTrackers                                     Кол-во трекеров привязанных к данной организации
 * @property int $countExperts                                      Кол-во экспертов привязанных к данной организации
 * @property int $countUsers                                        Кол-во проектантов привязанных к данной организации
 * @property int $countProjects                                     Кол-во проектов привязанных к данной организации
 * @property CustomerExpert[] $customerExperts                      Эксперты Spaccel, которые когда-либо были привязаны к организации
 * @property CustomerWishList[] $customerWishLists                  Доступы организаций к спискам запросов компаний B2B сегмента
 * @property ClientCodes[] $codes                                   Клиентские коды
 */
class Client extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'client';
    }


    /**
     * Получить все записи по клиенту
     * в таблице client_activation
     *
     * @return ActiveQuery
     */
    public function getClientActivationRecords(): ActiveQuery
    {
        return $this->hasMany(ClientActivation::class, ['client_id' => 'id']);
    }


    /**
     * Получить все записи по клиенту
     * в таблице client_codes
     *
     * @return ActiveQuery
     */
    public function getCodes(): ActiveQuery
    {
        return $this->hasMany(ClientCodes::class, ['client_id' => 'id']);
    }


    /**
     * Найти последнюю (актуальную) запись по клиенту
     * в таблице client_activation
     *
     * @return ActiveRecord|null
     */
    public function findClientActivation(): ?ActiveRecord
    {
        return ClientActivation::find()
            ->andWhere(['client_id' => $this->getId()])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }


    /**
     * Проверить активна ли в данный момент организация
     *
     * @return bool
     */
    public function isActive(): bool
    {
        /** @var ClientActivation $clientActivation */
        $clientActivation = $this->findClientActivation();
        return $clientActivation->getStatus() === ClientActivation::ACTIVE;
    }


    /**
     * Найти всех активированных клиентов
     *
     * @return array
     */
    public static function findAllActiveClients(): array
    {
        $clients = self::find()->all();
        $result = array();

        foreach ($clients as $client) {
            /** @var ClientActivation $clientActivation */
            if (($clientActivation = $client->findClientActivation()) && $clientActivation->getStatus() === ClientActivation::ACTIVE) {
                $result[] = $client;
            }
        }
        return $result;
    }


    /**
     * Получить настройки клиента
     *
     * @return ActiveQuery
     */
    public function getSettings(): ActiveQuery
    {
        return $this->hasOne(ClientSettings::class, ['client_id' => 'id']);
    }


    /**
     * Получить все тарифы на которые
     * когда либо была подписана организация (клиент)
     *
     * @return ActiveQuery
     */
    public function getClientRatesPlans(): ActiveQuery
    {
        return $this->hasMany(ClientRatesPlan::class, ['client_id' => 'id']);
    }


    /**
     * Получить последний установленный тариф для организации(клиента)
     *
     * @return ActiveRecord|null
     */
    public function findLastClientRatesPlan(): ?ActiveRecord
    {
        return ClientRatesPlan::find()
            ->andWhere(['client_id' => $this->getId()])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }


    /**
     * Поиск записей в таблице client_user
     * по данному клиенту
     *
     * @return ActiveQuery
     */
    public function getClientUsers(): ActiveQuery
    {
        return $this->hasMany(ClientUser::class, ['client_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCustomerManagers(): ActiveQuery
    {
        return $this->hasMany(CustomerManager::class, ['client_id' => 'id']);
    }


    /**
     * @return ActiveRecord|null
     */
    public function findCustomerManager(): ?ActiveRecord
    {
        return CustomerManager::find()->andWhere(['client_id' => $this->getId()])->orderBy(['created_at' => SORT_DESC])->one();
    }


    /**
     * @return CustomerTracker|null
     */
    public function getCustomerTrackers(): ?CustomerTracker
    {
        return CustomerTracker::findOne(['client_id' => $this->getId(), 'status' => CustomerTracker::ACTIVE]);
    }


    /**
     * Получить количество трекеров,
     * зарегистрированных в данной организации
     *
     * @return int
     */
    public function getCountTrackers(): int
    {
        return User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $this->getId(), 'role' => User::ROLE_ADMIN])->count();
    }


    /**
     * Получить количество экспертов,
     * зарегистрированных в данной организации
     *
     * @return int
     */
    public function getCountExperts(): int
    {
        return User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $this->getId(), 'role' => User::ROLE_EXPERT])->count();
    }


    /**
     * Получить количество проектантов,
     * зарегистрированных в данной организации
     *
     * @return int|string
     */
    public function getCountUsers()
    {
        return User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $this->getId(), 'role' => User::ROLE_USER])->count();
    }


    /**
     * Получить количество проектов,
     * зарегистрированных в данной организации
     *
     * @return int
     */
    public function getCountProjects(): int
    {
        $users = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $this->getId(), 'role' => User::ROLE_USER])->all();

        $arrayCountProjects = array();
        foreach ($users as $user) {
            $arrayCountProjects[] = Projects::find()->andWhere(['user_id' => $user->id])->count();
        }
        return array_sum($arrayCountProjects);
    }


    /**
     * @return CustomerExpert|null
     */
    public function getCustomerExperts(): ?CustomerExpert
    {
        return CustomerExpert::findOne(['client_id' => $this->getId(), 'status' => CustomerExpert::ACTIVE]);
    }


    /**
     * Найти пользователей зарегистрированных
     * на платформе в организации клиента
     *
     * @return array|ActiveRecord[]
     */
    public function findUsers(): array
    {
        return User::find()->with('clientUsers')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $this->getId()])->all();
    }


    /**
     * Проверка готовности организации к активации
     *
     * @return bool
     */
    public function checkingReadinessActivation(): bool
    {
        if ($this->findCustomerManager() && $this->findLastClientRatesPlan()) {
            return true;
        }
        return false;
    }


    /**
     * Получить списки запросов компаний B2B сегмента
     *
     * @return WishList[]
     */
    public function findWishLists(): array
    {
        $user = User::findOne(Yii::$app->user->getId());
        $mainAdmin = $user->mainAdmin;

        if (User::isUserMainAdmin($mainAdmin->getUsername())) {
            $customers = CustomerWishList::find()
                ->andWhere(['customer_id' => $this->getId(), 'deleted_at' => null])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

            $clientIds = [];
            foreach ($customers as $customer) {
                /** @var CustomerWishList $customer */
                if (!$customer->getDeletedAt() && !in_array($customer->getClientId(), $clientIds, true)) {
                    $clientIds[] = $customer->getClientId();
                }
            }

            if ($clientIds) {
                array_unshift($clientIds, $this->getId());
                return WishList::find()
                    ->andWhere(['in', 'client_id', $clientIds])
                    ->andWhere(['not', ['completed_at' => null]])
                    ->all();
            }

            return WishList::find()
                ->andWhere(['client_id' => $this->getId()])
                ->andWhere(['not', ['completed_at' => null]])
                ->all();
        }

        if (User::isUserAdminCompany($mainAdmin->getUsername())) {
            $mainAdminSpaccel = User::findOne(['role' => User::ROLE_MAIN_ADMIN]);
            $clientSpaccel = $mainAdminSpaccel->clientUser->client;
            $customer = CustomerWishList::find()
                ->andWhere([
                    'client_id' => $clientSpaccel->getId(),
                    'customer_id' => $this->getId(),
                    'deleted_at' => null
                ])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            /** @var CustomerWishList|null $customer */
            $existAccess = ($customer && !$customer->getDeletedAt());

            if ($existAccess) {

                $customers = CustomerWishList::find()
                    ->andWhere(['customer_id' => $clientSpaccel->getId(), 'deleted_at' => null])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->all();

                $clientIds = [];
                foreach ($customers as $customer) {
                    /** @var CustomerWishList $customer */
                    if (!$customer->getDeletedAt() && !in_array($customer->getClientId(), $clientIds, true)) {
                        $clientIds[] = $customer->getClientId();
                    }
                }

                array_unshift($clientIds, $this->getId(), $clientSpaccel->getId());
                return WishList::find()
                    ->andWhere(['in', 'client_id', $clientIds])
                    ->andWhere(['not', ['completed_at' => null]])
                    ->all();
            }

            return WishList::find()
                ->andWhere(['client_id' => $this->getId()])
                ->andWhere(['not', ['completed_at' => null]])
                ->all();
        }

        return [];
    }

    /**
     * @return ActiveQuery|null
     */
    public function findWishListsForPagination(): ?ActiveQuery
    {
        $user = User::findOne(Yii::$app->user->getId());

        if (User::isUserMainAdmin($user->getUsername())) {
            $customers = CustomerWishList::find()
                ->andWhere(['customer_id' => $this->getId(), 'deleted_at' => null])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

            $clientIds = [];
            foreach ($customers as $customer) {
                /** @var CustomerWishList $customer */
                if (!$customer->getDeletedAt() && !in_array($customer->getClientId(), $clientIds, true)) {
                    $clientIds[] = $customer->getClientId();
                }
            }

            if ($clientIds) {
                array_unshift($clientIds, $this->getId());
                return WishList::find()
                    ->andWhere(['in', 'client_id', $clientIds])
                    ->andWhere(['not', ['completed_at' => null]]);
            }

            return WishList::find()
                ->andWhere(['client_id' => $this->getId()])
                ->andWhere(['not', ['completed_at' => null]]);
        }

        if (User::isUserAdminCompany($user->getUsername())) {
            $mainAdminSpaccel = User::findOne(['role' => User::ROLE_MAIN_ADMIN]);
            $clientSpaccel = $mainAdminSpaccel->clientUser->client;
            $customer = CustomerWishList::find()
                ->andWhere([
                    'client_id' => $clientSpaccel->getId(),
                    'customer_id' => $this->getId(),
                    'deleted_at' => null
                ])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            /** @var CustomerWishList|null $customer */
            $existAccess = ($customer && !$customer->getDeletedAt());

            if ($existAccess) {

                $customers = CustomerWishList::find()
                    ->andWhere(['customer_id' => $clientSpaccel->getId(), 'deleted_at' => null])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->all();

                $clientIds = [];
                foreach ($customers as $customer) {
                    /** @var CustomerWishList $customer */
                    if (!$customer->getDeletedAt() && !in_array($customer->getClientId(), $clientIds, true)) {
                        $clientIds[] = $customer->getClientId();
                    }
                }

                array_unshift($clientIds, $this->getId(), $clientSpaccel->getId());
                return WishList::find()
                    ->andWhere(['in', 'client_id', $clientIds])
                    ->andWhere(['not', ['completed_at' => null]]);
            }

            return WishList::find()
                ->andWhere(['client_id' => $this->getId()])
                ->andWhere(['not', ['completed_at' => null]]);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isAccessGeneralWishList(): bool
    {
        $mainAdminSpaccel = User::findOne(['role' => User::ROLE_MAIN_ADMIN]);
        $clientSpaccel = $mainAdminSpaccel->clientUser->client;
        /** @var CustomerWishList|null $record */
        $record = CustomerWishList::find()
            ->andWhere([
                'client_id' => $clientSpaccel->getId(),
                'customer_id' => $this->getId(),
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        return ($record && !$record->getDeletedAt());
    }

    /**
     * @return bool
     */
    public function isAccessMyWishList(): bool
    {
        $mainAdminSpaccel = User::findOne(['role' => User::ROLE_MAIN_ADMIN]);
        $clientSpaccel = $mainAdminSpaccel->clientUser->client;
        /** @var CustomerWishList|null $record */
        $record = CustomerWishList::find()
            ->andWhere([
                'client_id' => $this->getId(),
                'customer_id' => $clientSpaccel->getId(),
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        return ($record && !$record->getDeletedAt());
    }

    /**
     * Получить доступы организаций к спискам запросов компаний B2B сегмента
     *
     * @return ActiveQuery
     */
    public function getCustomerWishLists(): ActiveQuery
    {
        return $this->hasMany(CustomerWishList::class, ['client_id' => 'id']);
    }


    /**
     * Поиск организации по id
     *
     * @param int $id
     * @return Client|null
     */
    public static function findById(int $id): ?Client
    {
        return self::findOne($id);
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
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getFullname(): string
    {
        return $this->fullname;
    }


    /**
     * @param string $fullname
     */
    public function setFullname(string $fullname): void
    {
        $this->fullname = $fullname;
    }


    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }


    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'fullname', 'city', 'description'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            ['name', 'string', 'min' => 3, 'max' => 32],
            [['fullname', 'city'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 2000],
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
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
            'city' => 'Город, в котором находится организация',
            'description' => 'Описание организации',
            'created_at' => 'Дата регистрации',
            'updated_at' => 'Дата обновления'
        ];
    }


    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->createClientActivationDefault();
        });

        parent::init();
    }


    /**
     * @return void
     */
    private function createClientActivationDefault(): void
    {
        $clientActivation = new ClientActivation();
        $clientActivation->setClientId($this->id);
        $clientActivation->save();
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

}