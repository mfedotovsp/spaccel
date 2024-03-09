<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Форма для авторизации на сайте
 *
 * Class LoginForm
 * @package app\models
 *
 * @property string $identity                   Логин или email пользователя
 * @property string $password                   Пароль пользователя
 * @property bool $rememberMe                   Флаг "Запомнить меня"
 * @property User|false $_user                  Объект авторизованного пользователя
 */
class LoginForm extends Model
{

    public $identity;
    public $password;
    public $rememberMe = true;
    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            ['identity', 'filter', 'filter' => 'trim'],
            ['identity', 'required'],
            ['identity', 'string'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'identity' => 'Логин или адрес эл.почты',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить',
        ];
    }


    /**
     * @param $attribute
     */
    public function validatePassword($attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->getPassword())) {
                $this->addError($attribute, 'Логин/пароль введены не верно!');
            }
        }
    }


    /**
     * @return bool
     */
    public function login(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user) {
                return Yii::$app->user->login($user, $this->isRememberMe() ? 3600 * 24 * 30 : 0);
            }
            return false;
        }
        return false;
    }


    /**
     * @return bool|User
     */
    public function getUser()
    {
        if (($this->_user === false) && $user = User::findIdentityByUsernameOrEmail($this->identity)) {
            $this->_user = $user;
        }
        return $this->_user;
    }


    /**
     * Подтвреждение регистрации по email
     * @param User $user
     * @return bool
     */
    public function sendActivationEmail(User $user): bool
    {
        return Yii::$app->mailer->compose('activationEmail', ['user' => $user])
            ->setFrom([Yii::$app->params['supportEmail'] => 'StartPool - Акселератор стартап-проектов'])
            ->setTo($user->getEmail())
            ->setSubject('Регистрация на сайте StartPool')
            ->send();

    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @param string $identity
     */
    public function setIdentity(string $identity): void
    {
        $this->identity = $identity;
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
     * @return bool
     */
    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }

    /**
     * @param bool $rememberMe
     */
    public function setRememberMe(bool $rememberMe): void
    {
        $this->rememberMe = $rememberMe;
    }

    /**
     * @param User|false $user
     */
    public function setUser($user): void
    {
        $this->_user = $user;
    }
}
