<?php

namespace app\models;

use app\modules\admin\models\ConversationMainAdmin;
use app\modules\admin\models\MessageMainAdmin;
use app\modules\admin\models\MessageManager;
use app\modules\expert\models\ConversationExpert;
use app\modules\expert\models\MessageExpert;
use app\services\MailerService;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * Класс, который хранит информацию о пользователях
 *
 * Class User
 * @package app\models
 *
 * @property int $id                                            Идентификатор пользователя
 * @property string $email                                      Адрес эл. почты пользователя
 * @property string $username                                   Логин пользователя
 * @property string $password_hash                              Хэшированный пароль пользователя хранится в бд
 * @property string $password                                   Пароль пользователя не хранится в бд
 * @property string $avatar_max_image                           Название загруженного файла с аватаром пользователя
 * @property string $avatar_image                               Название сформированного файла с аватаром пользователя
 * @property string $auth_key                                   Ключ авторизации пользователя (пока не используется)
 * @property string $secret_key                                 Секретный ключ для подтверждения регистрации (ограничен по времени действия)
 * @property int $role                                          Проектная роль пользователя
 * @property int $status                                        Статус пользователя
 * @property int $confirm                                       Подтверждена ли регистрация пользователя
 * @property int $id_admin                                      Поле для привязки проектанта к трекеру
 * @property int $created_at                                    Дата регистрации пользователя
 * @property int $updated_at                                    Дата обновления пользователя (его данных)
 *
 * @property Projects[] $projects                               Проекты пользователя
 * @property ContractorInfo $contractorInfo                     Получение информации о исполнителе от лица исполнителя
 * @property ContractorEducations[] $contractorEducations       Получение информации об образовании исполнителя от лица исполнителя
 * @property ContractorUsers[] $contractorUsers                 Связи исполнителей с проектантами
 * @property ContractorProject[] $contractorProjects            Связи исполнителей с проектами
 * @property ExpertInfo $expertInfo                             Информация о пользователе с ролью "Эксперт"
 * @property KeywordsExpert $keywords                           Ключевые слова о деятельности эксперта
 * @property UserAccessToProjects[] $userAccessToProjects       Все записи о доступе пользователя (эксперта) к проектам
 * @property ClientUser $clientUser                             Связь пользователя и оргранизации
 * @property CustomerManager[] $customerManagers                Связь пользователя с ролью Менеджер от Spaccel с организациями, к которым он привязан
 * @property CustomerTracker[] $customerTrackers                Связь пользователя с ролью Трекер от Spaccel с организациями, к которым он привязан
 * @property CustomerExpert[] $customerExperts                  Связь пользователя с ролью Эксперт от Spaccel с организациями, к которым он привязан
 * @property CheckingOnlineUser $checkingOnline                 Проверка пользователя на статус онлайн
 * @property bool|string $checkOnline                           Получить статус пользователя онлайн или время посл.активности
 * @property User $mainAdmin                                    Получить объект главного админа или админа организации
 * @property User $admin                                        Получить объект трекера
 * @property User $development                                  Получить объект техподдержки
 * @property bool|int $countUnreadMessages                      Получить кол-во непрочитанных сообщений пользователя
 * @property bool|int $countUnreadCommunications                Получить кол-во непрочитанных уведомлений пользователя
 * @property bool|int $countUnreadCommunicationsFromContractors Получить кол-во непрочитанных уведомлений от исполнителей
 * @property bool|int $countUnreadMessagesFromAdmin             Получить кол-во непрочитанных сообщений проектанта от трекера
 * @property bool|int $countUnreadMessagesFromDev               Получить кол-во непрочитанных сообщений пользователя от техподдержки
 * @property bool|int $countUnreadMessagesFromMainAdmin         Получить кол-во непрочитанных сообщений трекера или менеджера от админа
 * @property bool|int $countUnreadMessagesExpertFromMainAdmin   Получить кол-во непрочитанных сообщений эксперта от админа
 * @property bool|int $countUnreadMessagesFromUser              Получить кол-во непрочитанных сообщений от проектанта трекеру, где проектант является отправителем
 * @property bool|int $countUnreadMessagesDevelopmentFromUser   Получить кол-во непрочитанных сообщений от пользователя техподдержке, где пользователь является отправителем
 * @property bool|int $countUnreadMessagesMainAdminFromAdmin    Получить кол-во непрочитанных сообщений от трекера админу, где трекер является отправителем
 * @property bool|int $countUnreadMessagesMainAdminFromExpert   Получить кол-во непрочитанных сообщений от эксперта админу, где эксперт является отправителем
 * @property bool|int $countUnreadMessagesMainAdminFromManager  Получить кол-во непрочитанных сообщений от менеджера админу, где менеджер является отправителем
 */
class User extends ActiveRecord implements IdentityInterface
{

    public const STATUS_DELETED = 0; // Заблокирован
    public const STATUS_NOT_ACTIVE = 1; // Не активирован
    public const STATUS_ACTIVE = 10; // Активирован

    public const ROLE_USER = 10;           // Роль проектанта
    public const ROLE_CONTRACTOR = 15;     // Роль исполнителя проекта
    public const ROLE_ADMIN = 20;          // Роль трекера
    public const ROLE_ADMIN_COMPANY = 25;  // Роль администратора организации
    public const ROLE_MAIN_ADMIN = 30;     // Роль гл.администратора платформы
    public const ROLE_EXPERT = 40;         // Роль эксперта
    public const ROLE_MANAGER = 50;        // Роль менеждера по клиентам (организациям) от платформы
    public const ROLE_DEV = 100;           // Роль тех.поддержки

    public const CONFIRM = 20; // Регистрация подтверждена
    public const NOT_CONFIRM = 10; // Регистрация не подтверждена

    public $password;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['username', 'email', 'password', 'avatar_max_image', 'avatar_image'], 'filter', 'filter' => 'trim'],
            [['email', 'avatar_max_image', 'avatar_image'], 'string', 'max' => 255],
            [['username', 'email'], 'required'],
            [['role', 'status', 'confirm', 'id_admin'], 'integer'],
            ['email', 'email'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['password', 'string', 'min' => 6, 'max' => 32],
            ['password', 'required', 'on' => 'create'],
            ['username', 'unique', 'message' => 'Этот логин уже занят'],
            ['email', 'unique', 'message' => 'Эта почта уже зарегистрирована'],
            ['secret_key', 'unique'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'password' => 'Password',
            'status' => 'Статус',
            'role' => 'Проектная роль',
            'created_at' => 'Дата регистрации',
            'updated_at' => 'Последнее изменение',
        ];
    }


    public function init()
    {
        $this->on(self::EVENT_AFTER_DELETE, function (){
            if ($expertInfo = $this->expertInfo){
                $expertInfo->delete();
            }
            if ($keywords = $this->keywords) {
                $keywords->delete();
            }
        });

        parent::init();
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }


    /**
     * Получить все проекты пользователя
     *
     * @return ActiveQuery
     */
    public function getProjects(): ActiveQuery
    {
        return $this->hasMany(Projects::class, ['user_id' => 'id']);
    }


    /**
     * Получить подробную
     * информацию o эсперте
     *
     * @return ActiveQuery
     */
    public function getExpertInfo(): ActiveQuery
    {
        return $this->hasOne(ExpertInfo::class, ['user_id' => 'id']);
    }


    /**
     * Получить ключевые слова
     * о деятельности эксперта
     *
     * @return ActiveQuery
     */
    public function getKeywords(): ActiveQuery
    {
        return $this->hasOne(KeywordsExpert::class, ['expert_id' => 'id']);
    }


    /**
     * Получить объекты доступа класса UserAccessToProjects
     * стороннего пользователя к проектам
     *
     * @return ActiveQuery
     */
    public function getUserAccessToProjects(): ActiveQuery
    {
        return $this->hasMany(UserAccessToProjects::class, ['user_id' => 'id']);
    }


    /**
     * Получить объект доступа класса UserAccessToProjects
     * стороннего пользователя к конкретному проекту
     *
     * @param int $id
     * @return array|ActiveRecord|null
     */
    public function findUserAccessToProject(int $id)
    {
        return UserAccessToProjects::find()
            ->andWhere(['user_id' => $this->getId()])
            ->andWhere(['project_id' => $id])
            ->orderBy('id DESC')
            ->one();
    }


    /**
     * Аутентификация пользователей
     *
     * @param int|string $id
     * @return User|IdentityInterface|null
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'confirm' => self::CONFIRM,
        ]);
    }


    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return static::findOne(['access_token' => $token]);
    }


    /**
     * Находит пользователя по имени и возвращает объект найденного пользователя
     *
     * @param string $username
     * @return User|null
     */
    public static function findByUsername(string $username): ?User
    {
        return static::findOne(['username' => $username]);
    }


    /**
     * Находит пользователя по емайл
     *
     * @param string $email
     * @return User|null
     */
    public static function findByEmail(string $email): ?User
    {
        return static::findOne(['email' => $email]);
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
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }


    /**
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }


    /**
     * Сравнивает полученный пароль с паролем в поле password_hash, для текущего пользователя, в таблице user.
     * Вызываеться из модели LoginForm.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }


    /**
     * Генерирует случайную строку из 32 шестнадцатеричных символов и присваивает (при записи) полученное значение полю auth_key
     * таблицы user для нового пользователя.
     * Вызываеться из модели RegForm.
     *
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }


    /**
     * Генерирует хеш из введенного пароля и присваивает (при записи)
     * полученное значение полю password_hash таблицы user для нового пользователя.
     * Вызываеться из модели SingupForm.
     *
     * @param string $password
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }


    /**
     * Поиск пользователя по переданному секретному ключу
     * для смены пароля через почту
     *
     * @param string $key
     * @return User|null
     */
    public static function findBySecretKey(string $key): ?User
    {
        if (!static::isSecretKeyExpire($key)) {
            return null;
        }
        return static::findOne([
            'secret_key' => $key,
        ]);
    }


    /**
     * Генерация секретного ключа
     * для смены пароля через почту
     *
     * @throws Exception
     */
    public function generateSecretKey(): void
    {
        $this->secret_key = Yii::$app->security->generateRandomString() . '_' . time();
    }


    /**
     * Удаление секретного ключа
     * для смены пароля через почту
     */
    public function removeSecretKey(): void
    {
        $this->secret_key = null;
    }


    /**
     * Проверка срока действия секретного ключ
     *
     * @param string $key
     * @return bool
     */
    public static function isSecretKeyExpire(string $key): bool
    {
        if (empty($key)) {
            return false;
        }
        $expire = Yii::$app->params['secretKeyExpire'];
        $parts = explode('_', $key);
        $timestamp = (int)end($parts);

        return $timestamp + $expire >= time();
    }


    /**
     * Поиск пользователя по email или login
     *
     * @param string $identity
     * @return User|bool
     */
    public static function findIdentityByUsernameOrEmail(string $identity)
    {
        /** @var User $user */
        $user = self::find()->andWhere(['or', ['email' => $identity], ['username' => $identity]])->one();
        return $user ?: false;
    }


    /**
     * Получение записи в таблице client_user
     * по данному пользователю
     *
     * @return ActiveQuery
     */
    public function getClientUser(): ActiveQuery
    {
        return $this->hasOne(ClientUser::class, ['user_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCustomerManagers(): ActiveQuery
    {
        return $this->hasMany(CustomerManager::class, ['user_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCustomerTrackers(): ActiveQuery
    {
        return $this->hasMany(CustomerTracker::class, ['user_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCustomerExperts(): ActiveQuery
    {
        return $this->hasMany(CustomerExpert::class, ['user_id' => 'id']);
    }


    /**
     * Получить объект проверки статуса онлайн
     *
     * @return ActiveQuery
     */
    public function getCheckingOnline(): ActiveQuery
    {
        return $this->hasOne(CheckingOnlineUser::class, ['user_id' => 'id']);
    }


    /**
     * Получить статус пользователя онлайн или время посл.активности
     *
     * @return bool|string
     */
    public function getCheckOnline()
    {
        if ($checkingOnline = $this->checkingOnline) {
            return $checkingOnline->isOnline();
        }
        return false;
    }


    /**
     * Получить объект главного админа или админа организации
     *
     * @return User|null
     */
    public function getMainAdmin(): ?User
    {
        if ($this->role !== self::ROLE_ADMIN_COMPANY) {
            $mainAdminId = ClientSettings::find()
                ->select('admin_id')
                ->andWhere(['client_id' => $this->clientUser->getClientId()])
                ->one();

            return self::findOne($mainAdminId);
        }
        return self::findOne(['role' => self::ROLE_MAIN_ADMIN]);
    }


    /**
     * Получить объект трекера
     *
     * @return bool|ActiveQuery
     */
    public function getAdmin()
    {
        if ($this->role === self::ROLE_USER) {
            return $this->hasOne(self::class, ['id' => 'id_admin']);
        }
        return false;
    }


    /**
     * Получить объект техподдержки
     *
     * @return User|null
     */
    public function getDevelopment(): ?User
    {
        return static::findOne(['role' => static::ROLE_DEV]);
    }


    /**
     * Отправка письма на почту пользователю при изменении его статуса
     *
     * @return bool
     */
    public function sendEmailUserStatus(): bool
    {
        /* @var $user User */
        $user = static::findOne(['email' => $this->email]);

        if($user){
            return MailerService::send(
                $this->getEmail(),
                'Изменение Вашего статуса на сайте ' . Yii::$app->params['siteName'],
                'change-status',
                ['user' => $user]
            );
        }
        return false;
    }


    /**
     * Создание беседы техподдержки и пользователя при активации его статуса
     *
     * @return ConversationDevelopment|null
     */
    public function createConversationDevelopment(): ?ConversationDevelopment
    {
        $con = ConversationDevelopment::findOne(['user_id' => $this->getId()]);

        if (!$con) {
            $conversation = new ConversationDevelopment();
            $conversation->setUserId($this->getId());
            $conversation->setDevId($this->development->getId());
            return $conversation->save() ? $conversation : null;
        }

        return $con;
    }


    /**
     * Создание беседы админа организации и трекера
     *
     * @return ConversationMainAdmin|null
     */
    public function createConversationMainAdmin(): ?ConversationMainAdmin
    {
        $mainAdmin = $this->mainAdmin;
        $con = ConversationMainAdmin::findOne([
            'main_admin_id' => $mainAdmin->getId(),
            'admin_id' => $this->getId()
        ]);

        if (!$con) {
            $conversation = new ConversationMainAdmin();
            $conversation->setAdminId($this->getId());
            $conversation->setMainAdminId($mainAdmin->getId());
            return $conversation->save() ? $conversation : null;
        }

        return $con;
    }


    /**
     * Создание беседы трекером и проектанта
     *
     * @param User $user
     * @return ConversationAdmin|null
     */
    public function createConversationAdmin(User $user): ?ConversationAdmin
    {
        $con = ConversationAdmin::findOne(['user_id' => $user->getId()]);

        if (!$con) {
            $conversation = new ConversationAdmin();
            $conversation->setUserId($user->getId());
            $conversation->setAdminId($user->getIdAdmin());
            return $conversation->save() ? $conversation : null;
        }

        return $con;
    }


    /**
     * Создание беседы любого пользователя (только не эксперта) и
     * эксперта при активации его статуса
     *
     * @param User $user
     * @param User $expert
     * @return ConversationExpert|null
     */
    public static function createConversationExpert(User $user, User $expert): ?ConversationExpert
    {
        $con = ConversationExpert::findOne(['user_id' => $user->getId(), 'expert_id' => $expert->getId()]);

        if (!$con) {
            $conversation = new ConversationExpert();
            $conversation->setUserId($user->getId());
            $conversation->setExpertId($expert->getId());
            $conversation->setRole($user->getRole());
            return $conversation->save() ? $conversation : null;
        }

        return $con;

    }


    /**
     * Отправка письма админу организации
     *
     * @param User $user
     * @return bool
     */
    public function sendEmailAdmin(User $user): bool
    {
        if($user) {

            $admin = $user->mainAdmin;

            return MailerService::send(
                $admin->getEmail(),
                'Регистрация нового пользователя на сайте ' . Yii::$app->params['siteName'],
                'signup-admin',
                ['user' => $user]
            );
        }
        return false;
    }


    /**
     * Общее кол-во непрочитанных
     * сообщений пользователя
     *
     * @return bool|int
     */
    public function getCountUnreadMessages()
    {
        $count = 0;

        if (self::isUserSimple($this->getUsername())) {

            $countUnreadMessagesAdmin = MessageAdmin::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageAdmin::NO_READ_MESSAGE])->count();
            $countUnreadMessagesDev = MessageDevelopment::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
            $countUnreadMessagesExpert = MessageExpert::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();
            $count = ($countUnreadMessagesAdmin + $countUnreadMessagesDev + $countUnreadMessagesExpert);
        }
        elseif (self::isUserAdmin($this->getUsername())) {

            $countUnreadMessagesAdmin = MessageAdmin::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageAdmin::NO_READ_MESSAGE])->count();
            $countUnreadMessagesMainAdmin = MessageMainAdmin::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageMainAdmin::NO_READ_MESSAGE])->count();
            $countUnreadMessagesDev = MessageDevelopment::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
            $countUnreadMessagesExpert = MessageExpert::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();
            $count = ($countUnreadMessagesAdmin + $countUnreadMessagesMainAdmin + $countUnreadMessagesDev + $countUnreadMessagesExpert);
        }
        elseif (self::isUserMainAdmin($this->getUsername()) || self::isUserAdminCompany($this->getUsername())) {

            $countUnreadMessagesMainAdmin = MessageMainAdmin::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageMainAdmin::NO_READ_MESSAGE])->count();
            $countUnreadMessagesDev = MessageDevelopment::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
            $countUnreadMessagesExpert = MessageExpert::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();
            $countUnreadMessagesManager = MessageManager::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageManager::NO_READ_MESSAGE])->count();
            $count = ($countUnreadMessagesMainAdmin + $countUnreadMessagesDev + $countUnreadMessagesExpert + $countUnreadMessagesManager);
        }
        elseif (self::isUserDev($this->getUsername())) {

            $count = MessageDevelopment::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
        }
        elseif (self::isUserExpert($this->getUsername())) {

            $countUnreadMessagesExpert = MessageExpert::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();
            $countUnreadMessagesDev = MessageDevelopment::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
            $count = ($countUnreadMessagesExpert + $countUnreadMessagesDev);
        }
        elseif (self::isUserManager($this->getUsername())) {

            $countUnreadMessagesManager = MessageManager::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageManager::NO_READ_MESSAGE])->count();
            $countUnreadMessagesDev = MessageDevelopment::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
            $count = ($countUnreadMessagesManager + $countUnreadMessagesDev);
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Общее кол-во непрочитанных
     * уведомлений пользователя
     *
     * @return bool|int
     */
    public function getCountUnreadCommunications()
    {
        $count = 0;

        if (self::isUserExpert($this->getUsername())) {

            $countUnreadProjectCommunications = ProjectCommunications::find()->andWhere(['adressee_id' => $this->getId(), 'status' => ProjectCommunications::NO_READ])->count();
            $count += $countUnreadProjectCommunications;
        }
        elseif (self::isUserMainAdmin($this->getUsername()) || self::isUserAdminCompany($this->getUsername())) {

            $countUnreadProjectCommunications = ProjectCommunications::find()->andWhere(['adressee_id' => $this->getId(), 'status' => ProjectCommunications::NO_READ])->count();
            $count += $countUnreadProjectCommunications;
        }
        elseif (self::isUserAdmin($this->getUsername())) {

            $countDuplicateCommunications = DuplicateCommunications::find()->andWhere(['adressee_id' => $this->getId(), 'status' => DuplicateCommunications::NO_READ])->count();
            $count += $countDuplicateCommunications;
        }
        elseif (self::isUserSimple($this->getUsername())) {

            $countDuplicateCommunications = DuplicateCommunications::find()->andWhere(['adressee_id' => $this->getId(), 'status' => DuplicateCommunications::NO_READ])->count();
            $count += $countDuplicateCommunications;
        }
        elseif (self::isUserContractor($this->getUsername())) {

            $countContractorCommunications = ContractorCommunications::find()->andWhere(['adressee_id' => $this->getId(), 'status' => ContractorCommunications::STATUS_NO_READ])->count();
            $count += $countContractorCommunications;
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных
     * уведомлений от исполнителей
     *
     * @return bool|int
     */
    public function getCountUnreadCommunicationsFromContractors()
    {
        $count = 0;

        if (self::isUserSimple($this->getUsername())) {
            $countContractorCommunications = ContractorCommunications::find()->andWhere(['adressee_id' => $this->getId(), 'status' => ContractorCommunications::STATUS_NO_READ])->count();
            $count += $countContractorCommunications;
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных
     * уведомлений от исполнителей
     *
     * @return bool|int
     */
    public function getCountUnreadCommunicationsByContractor(int $contractorId)
    {
        $count = 0;

        if (self::isUserSimple($this->getUsername())) {
            $countContractorCommunications = ContractorCommunications::find()
                ->andWhere([
                    'adressee_id' => $this->getId(),
                    'status' => ContractorCommunications::STATUS_NO_READ,
                    'sender_id' => $contractorId
                ])->count();
            $count += $countContractorCommunications;
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Количество непрочитанных
     * уведомлений пользователя
     * по проекту
     *
     * @param int $id
     * @return bool|int|string
     */
    public function getCountUnreadCommunicationsByProject(int $id, int $senderId = null)
    {
        $count = 0;

        if (self::isUserExpert($this->getUsername())) {

            $countUnreadProjectCommunications = ProjectCommunications::find()
                ->andWhere([
                    'adressee_id' => $this->getId(),
                    'status' => ProjectCommunications::NO_READ,
                    'project_id' => $id
                ])->count();

            $count += $countUnreadProjectCommunications;
        }

        if (self::isUserMainAdmin($this->getUsername())) {

            $countUnreadProjectCommunications = ProjectCommunications::find()
                ->andWhere([
                    'adressee_id' => $this->getId(),
                    'status' => ProjectCommunications::NO_READ,
                    'project_id' => $id
                ])->count();

            $count += $countUnreadProjectCommunications;
        }

        if (self::isUserContractor($this->getUsername())) {

            $countUnreadContractorCommunications = ContractorCommunications::find()
                ->andWhere([
                    'adressee_id' => $this->getId(),
                    'status' => ContractorCommunications::STATUS_NO_READ,
                    'project_id' => $id
                ])->count();

            $count += $countUnreadContractorCommunications;
        }

        if (self::isUserSimple($this->getUsername())) {

            $contractorCommunications = ContractorCommunications::find()
                ->andWhere([
                    'adressee_id' => $this->getId(),
                    'status' => ContractorCommunications::STATUS_NO_READ,
                    'project_id' => $id
                ]);

            if ($senderId) {
                $contractorCommunications = $contractorCommunications->andWhere(['sender_id' => $senderId]);
            }

            $count += $contractorCommunications->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений проектанта от трекера
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesFromAdmin()
    {
        $count = 0;

        if (self::isUserSimple($this->getUsername())) {

            $count = MessageAdmin::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageAdmin::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений от Техподдержки
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesFromDev()
    {
        $count = MessageDevelopment::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений
     * менеджера или трекера от админа
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesFromMainAdmin()
    {
        $count = 0;

        if (self::isUserAdmin($this->getUsername())) {

            $count = MessageMainAdmin::find()->andWhere(['adressee_id' => $this->getId(), 'status' => MessageMainAdmin::NO_READ_MESSAGE])->count();
        }

        elseif (self::isUserManager($this->getUsername())) {

            $count = MessageManager::find()->andWhere(['sender_id' => $this->mainAdmin->getId(), 'adressee_id' => $this->getId(), 'status' => MessageManager::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений эксперта от админа
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesExpertFromMainAdmin ()
    {
        $count = 0;

        if (self::isUserExpert($this->getUsername())) {

            $count = MessageExpert::find()->andWhere(['sender_id' => $this->mainAdmin->getId(), 'adressee_id' => $this->getId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений от проектанта трекеру,
     * где проектант является отправителем
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesFromUser()
    {
        $count = 0;

        if (self::isUserSimple($this->getUsername())) {

            $count = MessageAdmin::find()->andWhere(['sender_id' => $this->getId(), 'status' => MessageAdmin::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений от пользователя техподдержке,
     * где пользователь является отправителем
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesDevelopmentFromUser()
    {
        $count = MessageDevelopment::find()->andWhere(['sender_id' => $this->getId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений от трекера админу,
     * где он является отправителем
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesMainAdminFromAdmin()
    {
        $count = 0;

        if (self::isUserAdmin($this->getUsername())) {

            $count = MessageMainAdmin::find()->andWhere(['sender_id' => $this->getId(), 'status' => MessageMainAdmin::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений эксперта,
     * где он является отправителем для админа
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesMainAdminFromExpert()
    {
        $count = 0;

        if (self::isUserExpert($this->getUsername())) {

            $count = MessageExpert::find()->andWhere(['adressee_id' => $this->mainAdmin->getId(), 'sender_id' => $this->getId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений менеджера,
     * где он является отправителем для админа Spaccel
     *
     * @return bool|int
     */
    public function getCountUnreadMessagesMainAdminFromManager()
    {
        $count = 0;

        if (self::isUserManager($this->username)) {

            $count = MessageManager::find()->andWhere(['adressee_id' => $this->mainAdmin->getId(), 'sender_id' => $this->getId(), 'status' => MessageManager::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений пользователя от менеджера
     *
     * @param int $userId
     * @return bool|int
     */
    public function getCountUnreadMessagesFromManager(int $userId)
    {
        $count = 0;

        if (self::isUserManager($this->getUsername())) {

            $count = MessageManager::find()->andWhere(['adressee_id' => $userId, 'sender_id' => $this->getId(), 'status' => MessageManager::NO_READ_MESSAGE])->count();
        }

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений эксперта от пользователя
     *
     * @param int $id
     * @return bool|int
     */
    public function getCountUnreadMessagesUserFromExpert(int $id)
    {

        $count = MessageExpert::find()->andWhere(['adressee_id' => $id, 'sender_id' => $this->getId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();

        return ($count > 0) ? $count : false;
    }


    /**
     * Кол-во непрочитанных сообщений
     * от эксперта для пользователя
     *
     * @param int $id
     * @return bool|int
     */
    public function getCountUnreadMessagesExpertFromUser(int $id)
    {

        $count = MessageExpert::find()->andWhere(['adressee_id' => $this->getId(), 'sender_id' => $id, 'status' => MessageExpert::NO_READ_MESSAGE])->count();

        return ($count > 0) ? $count : false;
    }


    /**
     * Количество непрочитанных сообщений у менеджера от пользователя
     * (админа организации, трекера или админа Spaccel)
     *
     * @param int $id
     * @return bool|int
     */
    public function getCountUnreadMessagesManager(int $id)
    {

        $count = MessageManager::find()->andWhere(['adressee_id' => $this->getId(), 'sender_id' => $id, 'status' => MessageManager::NO_READ_MESSAGE])->count();

        return ($count > 0) ? $count : false;
    }


    /**
     * Получение связей исполнителей с проектами
     *
     * @return ActiveQuery
     */
    public function getContractorProjects(): ActiveQuery
    {
        return $this->hasMany(ContractorProject::class, ['contractor_id' => 'id']);
    }


    /**
     * Получение количества проектов у исполнителя
     *
     * @return int
     */
    public function getCountContractorProjects(): int
    {
        return (int)ContractorProject::find()
            ->select(['contractor_id', 'project_id'])
            ->distinct('project_id')
            ->andWhere(['contractor_id' => $this->getId()])
            ->andWhere(['deleted_at' => null])
            ->count();
    }


    /**
     * Получение количества
     * моих проектов у исполнителя.
     * Запрос делается от лица проектанта
     *
     * @return int
     */
    public function getCountContractorMyProjects(): int
    {
        $projects = Projects::find()
            ->andWhere(['user_id' => Yii::$app->user->getId()])
            ->all();

        return (int)ContractorProject::find()
            ->select(['contractor_id', 'project_id'])
            ->distinct('project_id')
            ->andWhere(['contractor_id' => $this->getId()])
            ->andWhere(['deleted_at' => null])
            ->andWhere(['in', 'project_id', array_column($projects, 'id')])
            ->count();
    }


    /**
     * Получение информации от лица исполнителя
     *
     * @return ActiveQuery
     */
    public function getContractorInfo(): ActiveQuery
    {
        return $this->hasOne(ContractorInfo::class, ['contractor_id' => 'id']);
    }


    /**
     * Получение информации об образовании от лица исполнителя
     *
     * @return ActiveQuery
     */
    public function getContractorEducations(): ActiveQuery
    {
        return $this->hasMany(ContractorEducations::class, ['contractor_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getContractorUsers(): ActiveQuery
    {
        if (self::isUserContractor(Yii::$app->user->identity['username'])) {
            return $this->hasMany(ContractorUsers::class, ['contractor_id' => 'id']);
        }

        return $this->hasMany(ContractorUsers::class, ['user_id' => 'id']);
    }


    /**
     * @return int|null
     */
    public function getAdditionalDateContractor(): ?int
    {
        if (self::isUserSimple(Yii::$app->user->identity['username']) && self::isUserContractor($this->getUsername())) {

            $projects = Projects::findAll(['user_id' => Yii::$app->user->getId()]);

            $projectIds = [];
            foreach ($projects as $project) {
                $projectIds[] = $project->getId();
            }

            /** @var $firstContractorProject ContractorProject */
            $firstContractorProject = ContractorProject::find()
                ->andWhere([
                    'contractor_id' => $this->getId(),
                    'project_id' => $projectIds
                ])->orderBy(['created_at' => SORT_ASC])
                ->one();

            return $firstContractorProject ? $firstContractorProject->getCreatedAt() : null;
        }

        return null;
    }


    /**
     * Проверка на проектанта (руководителя проекта)
     *
     * @param string $username
     * @return bool
     */
    public static function isUserSimple(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_USER, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на исполнителя проекта
     *
     * @param string $username
     * @return bool
     */
    public static function isUserContractor(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_CONTRACTOR, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на трекера
     *
     * @param string $username
     * @return bool
     */
    public static function isUserAdmin(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_ADMIN, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на Главного Админа
     *
     * @param string $username
     * @return bool
     */
    public static function isUserMainAdmin(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_MAIN_ADMIN, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на Эксперта
     *
     * @param string $username
     * @return bool
     */
    public static function isUserExpert(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_EXPERT, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на Техподдержку
     *
     * @param string $username
     * @return bool
     */
    public static function isUserDev(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_DEV, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на менеджера по клиентам
     *
     * @param string $username
     * @return bool
     */
    public static function isUserManager(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_MANAGER, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на администратора организации
     *
     * @param string $username
     * @return bool
     */
    public static function isUserAdminCompany(string $username): bool
    {
        if (static::findOne(['username' => $username, 'role' => self::ROLE_ADMIN_COMPANY, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }


    /**
     * Проверка на Статус
     *
     * @param string $username
     * @return bool
     */
    public static function isActiveStatus(string $username): bool
    {
        if (static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE])) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getAvatarMaxImage(): ?string
    {
        return $this->avatar_max_image;
    }

    /**
     * @param string|null $avatar_max_image
     */
    public function setAvatarMaxImage(?string $avatar_max_image = null): void
    {
        $this->avatar_max_image = $avatar_max_image;
    }

    /**
     * @return string|null
     */
    public function getAvatarImage(): ?string
    {
        return $this->avatar_image;
    }

    /**
     * @param string|null $avatar_image
     */
    public function setAvatarImage(?string $avatar_image = null): void
    {
        $this->avatar_image = $avatar_image;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    public function getTextRole(): string
    {
        switch ($this->getRole()) {
            case self::ROLE_USER:
                return 'проектант';
            case self::ROLE_ADMIN:
                return 'трекер';
            case self::ROLE_ADMIN_COMPANY:
                return 'адм.организации';
            case self::ROLE_MAIN_ADMIN:
                return 'адм.платформы';
            case self::ROLE_EXPERT:
                return 'эксперт';
            case self::ROLE_MANAGER:
                return 'менеджер';
            case self::ROLE_DEV:
                return 'тех.поддержка';
            case self::ROLE_CONTRACTOR:
                return 'исполнитель';
            default:
                return '';
        }
    }

    /**
     * @param int $role
     */
    public function setRole(int $role): void
    {
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getConfirm(): int
    {
        return $this->confirm;
    }

    /**
     * @param int $confirm
     */
    public function setConfirm(int $confirm): void
    {
        $this->confirm = $confirm;
    }

    /**
     * @return int|null
     */
    public function getIdAdmin(): ?int
    {
        return $this->id_admin;
    }

    /**
     * @param int $id_admin
     */
    public function setIdAdmin(int $id_admin): void
    {
        $this->id_admin = $id_admin;
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
