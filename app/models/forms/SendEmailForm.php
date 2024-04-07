<?php


namespace app\models\forms;

use yii\base\Exception;
use yii\base\Model;
use app\models\User;
use Yii;

/**
 * Форма отправки письма на почту для восстановления пароля
 *
 * Class SendEmailForm
 * @package app\models\forms
 *
 * @property string $email              Адрес эл.почты указанный при регистрации
 */
class SendEmailForm extends Model
{

    public $email;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Эл.почта'
        ];
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function sendEmail(): bool
    {
        /* @var $user User */
        $user = User::findOne(['email' => $this->getEmail()]);

        if($user){

            $user->generateSecretKey();

            if($user->save()){

                return Yii::$app->mailer->compose('resetPassword', ['user' => $user])
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['siteName'] . ' - Акселератор стартап-проектов'])
                    ->setTo($this->getEmail())
                    ->setSubject('Изменение пароля на сайте '. Yii::$app->params['siteName'] . ' для пользователя ' . $user->getUsername())
                    ->send();
            }
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
}
