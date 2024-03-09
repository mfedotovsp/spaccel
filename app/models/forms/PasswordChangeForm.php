<?php


namespace app\models\forms;

use yii\base\Exception;
use yii\base\Model;
use app\models\User;

/**
 * Форма для изменения пароля пользователя
 *
 * Class PasswordChangeForm
 * @package app\models\forms
 *
 * @property string $currentPassword                Текущий пароль
 * @property string $newPassword                    Новый пароль
 * @property string $newPasswordRepeat              Повторный ввод нового пароля
 * @property User $_user                            Объект текущего пользователя
 */
class PasswordChangeForm extends Model
{

    public $currentPassword;
    public $newPassword;
    public $newPasswordRepeat;


    /**
     * @var User
     */
    private $_user;


    /**
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, array $config = [])
    {
        $this->setUser($user);
        parent::__construct($config);
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['currentPassword', 'newPassword', 'newPasswordRepeat'], 'required'],
            [['currentPassword', 'newPassword', 'newPasswordRepeat'], 'string', 'min' => 6, 'max' => 32],
            [['currentPassword', 'newPassword', 'newPasswordRepeat'], 'spaceInPassword'],
            ['currentPassword', 'validatePassword'],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'newPassword' => 'Новый пароль',
            'newPasswordRepeat' => 'Повторите новый пароль',
            'currentPassword' => 'Актуальный пароль',
        ];
    }


    /**
     * @param string $attribute
     */
    public function validatePassword(string $attribute): void
    {
        if (!$this->hasErrors() && !$this->getUser()->validatePassword($this->$attribute)) {
            $this->addError($attribute, 'Ошибка! Неверный текущий пароль.');
        }
    }


    /**
     * @param $attr
     */
    public function spaceInPassword ($attr): void
    {
        if (preg_match('/\s+/',$this->$attr)) {
            $this->addError($attr, 'Не допускается использование пробелов');
        }
    }


    /**
     * @return boolean
     * @throws Exception
     */
    public function changePassword(): ?bool
    {
        if ($this->validate()) {
            $user = $this->_user;
            $user->setPassword($this->newPassword);
            return $user->save();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     */
    public function setCurrentPassword(string $currentPassword): void
    {
        $this->currentPassword = $currentPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string
     */
    public function getNewPasswordRepeat(): string
    {
        return $this->newPasswordRepeat;
    }

    /**
     * @param string $newPasswordRepeat
     */
    public function setNewPasswordRepeat(string $newPasswordRepeat): void
    {
        $this->newPasswordRepeat = $newPasswordRepeat;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->_user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->_user = $user;
    }

}