<?php


namespace app\models\forms;

use yii\base\Exception;
use yii\base\Model;
use app\models\User;
use Yii;

/**
 * Форма регистрации
 *
 * Class SingupForm
 * @package app\models\forms
 *
 * @property string $email
 * @property string $username
 * @property string $password
 * @property int $status
 * @property int $confirm
 * @property int $role
 * @property int $clientId
 */
class SingupForm extends Model
{

    public $email;
    public $username;
    public $uniq_username = true;
    public $match_username = true;
    public $uniq_email = true;
    public $password;
    public $status;
    public $confirm;
    public $role;
    public $clientId;
    public $exist_agree = true;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['exist_agree', 'uniq_username', 'uniq_email'],'boolean'],
            ['exist_agree', 'existAgree'],
            [['email', 'password'], 'required'],
            ['clientId', 'safe'],
            [['email', 'password'], 'trim'],
            [['email'], 'string', 'max' => 255],
            ['username', 'uniqUsername'],
            ['email', 'uniqEmail'],

            ['confirm', 'default', 'value' => User::NOT_CONFIRM, 'on' => 'emailActivation'],
            ['confirm', 'in', 'range' => [
                User::CONFIRM,
                User::NOT_CONFIRM,
            ]],

            ['status', 'default', 'value' => User::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => [
                User::STATUS_NOT_ACTIVE,
                User::STATUS_ACTIVE,
                User::STATUS_DELETED,
            ]],

            ['role', 'default', 'value' => User::ROLE_USER],
            ['role', 'in', 'range' => [
                User::ROLE_USER,
                User::ROLE_ADMIN,
                User::ROLE_MANAGER
            ]],

        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email *',
            'username' => 'Логин *',
            'password' => 'Пароль *',
            'role' => 'Проектная роль пользователя *',
            'clientId' => 'Организация, к которой будет привязан Ваш аккаунт *',
            'exist_agree' => ''
        ];
    }


    /**
     * Согласие на обработку данных
     * @param $attr
     */
    public function existAgree($attr): void
    {
        if ($this->exist_agree != 1){
            $this->addError($attr, 'Необходимо принять пользовательское соглашение');
        }
    }


    /**
     * @param $attr
     */
    public function uniqUsername($attr): void
    {
        if (User::findOne(['username' => $this->getUsername()])) {
            $this->uniq_username = false;
            $this->addError($attr, 'Этот логин уже занят.');
        }
    }


    /**
     * @param $attr
     */
    public function uniqEmail($attr): void
    {
        if (User::findOne(['email' => $this->getEmail()])) {
            $this->uniq_email = false;
            $this->addError($attr, 'Эта почта уже зарегистрирована.');
        }
    }


    /**
     * @return User|bool|null
     * @throws Exception
     */
    public function singup()
    {
        if ($this->exist_agree == 1){

            $user = new User();
            $user->setUsername($this->getEmail());
            $user->setEmail($this->getEmail());
            $user->setStatus($this->getStatus());
            $user->setConfirm($this->getConfirm());
            $user->setRole($this->getRole());
            $user->setPassword($this->getPassword());
            $user->generateAuthKey();

            if($this->scenario === 'emailActivation') {
                $user->generateSecretKey();
            }

            return $user->save() ? $user : null;
        }
        return false;
    }


    /**
     * Подтвреждение регистрации по email
     * @param User $user
     * @return bool
     */
    public function sendActivationEmail(User $user): bool
    {
        return Yii::$app->mailer->compose('activationEmail', ['user' => $user])
            ->setFrom([Yii::$app->params['supportEmail'] => 'Spaccel.ru - Акселератор стартап-проектов'])
            ->setTo($this->getEmail())
            ->setSubject('Регистрация на сайте Spaccel.ru')
            ->send();
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
     * @return SingupForm
     */
    public function setEmail(string $email): SingupForm
    {
        $this->email = $email;
        return $this;
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
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
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
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
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
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

}