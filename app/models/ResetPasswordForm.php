<?php


namespace app\models;

use yii\base\Exception;
use yii\base\Model;

/**
 * Форма для сброса пароля
 *
 * Class ResetPasswordForm
 * @package app\models
 *
 * @property string $password
 * @property bool $exist
 * @property User $_user
 */
class ResetPasswordForm extends Model
{

    public $password;
    public $exist = true;
    private $_user;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['password', 'string'],
            ['exist', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'password' => 'Пароль'
        ];
    }

    /**
     * ResetPasswordForm constructor.
     *
     * @param $key
     * @param array $config
     */
    public function __construct($key, array $config = [])
    {
        if(empty($key) || !is_string($key)) {
            $this->exist = false;
        } // Ключ не может быть пустым
        $this->_user = User::findBySecretKey($key);
        if(!$this->_user) {
            $this->exist = false;
        } // Не верный ключ
        parent::__construct($config);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function resetPassword(): bool
    {
        /* @var $user User */
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removeSecretKey();
        return $user->save();
    }
}