<?php


namespace app\modules\admin\models\form;

use app\models\User;
use yii\base\Exception;
use yii\base\Model;

/**
 * Форма создания админа организации при создании организации
 *
 * Class FormCreateAdminCompany
 * @package app\modules\admin\models\form
 *
 * @property string $email
 * @property string $username
 * @property int $status
 * @property int $confirm
 * @property int $role
 */
class FormCreateAdminCompany extends Model
{

    public $email;
    public $username;
    public $status = User::STATUS_ACTIVE;
    public $confirm = User::CONFIRM;
    public $role = User::ROLE_ADMIN_COMPANY;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'username'], 'required'],
            [['username', 'email'], 'trim'],
            [['email'], 'string', 'max' => 255],
            ['username', 'uniqUsername'],
            ['email', 'uniqEmail'],
            ['email', 'email'],

            ['confirm', 'default', 'value' => User::CONFIRM],
            ['confirm', 'in', 'range' => [User::CONFIRM]],

            ['status', 'default', 'value' => User::STATUS_ACTIVE],
            ['status', 'in', 'range' => [User::STATUS_ACTIVE]],

            ['role', 'default', 'value' => User::ROLE_ADMIN_COMPANY],
            ['role', 'in', 'range' => [User::ROLE_ADMIN_COMPANY]],

        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
            'username' => 'Логин',
        ];
    }


    /**
     * @param $attr
     */
    public function uniqUsername($attr): void
    {
        if (User::findOne(['username' => $this->getUsername()])) {
            $this->addError($attr, 'Этот логин уже занят.');
        }
    }


    /**
     * @param $attr
     */
    public function uniqEmail($attr): void
    {
        if (User::findOne(['email' => $this->getEmail()])){
            $this->addError($attr, 'Эта почта уже зарегистрирована.');
        }
    }


    /**
     * @return User|null
     * @throws Exception
     */
    public function create(): ?User
    {
        $user = new User();
        $user->attributes = $this->attributes;
        $user->setPassword($this->username); // Пароль такой же как и логин, чтобы не забыть (в дальнейшем клиент должен его поменять самостоятельно)
        $user->generateAuthKey();
        if ($user->save()) {
            $user->createConversationDevelopment();
            return $user;
        }
        return null;
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
}