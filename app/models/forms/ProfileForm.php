<?php


namespace app\models\forms;

use app\models\User;
use yii\base\Model;
use Yii;

/**
 * Форма для редактирования профиля пользователя
 *
 * Class ProfileForm
 * @package app\models\forms
 *
 * @property int $id                    Идентификатор пользователя
 * @property string $username           Логин пользователя
 * @property string $email              Эл.почта пользователя
 */
class ProfileForm extends Model
{

    public $id;
    public $username;
    public $email;
    public $uniq_username = true;
    public $match_username = true;
    public $uniq_email = true;
    public $checking_mail_sending = true;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['uniq_username', 'match_username', 'uniq_email', 'checking_mail_sending'], 'boolean'],
            [['username', 'email'], 'required'],
            [['username', 'email'], 'trim'],
            [['email'], 'string', 'max' => 255],
            ['username', 'uniqUsername'],
            ['username', 'matchUsername'],
            ['email', 'uniqEmail'],
        ];
    }


    /**
     * ProfileForm constructor.
     *
     * @param int $id
     * @param array $config
     */
    public function __construct(int $id, array $config = [])
    {
        $user = User::findOne($id);
        foreach ($user as $key => $value) {
            if (property_exists($this, $key)) {
                $this[$key] = $value;
            }
        }
        parent::__construct($config);
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
     * Собственное правило для поля username
     * Переводим все логины в нижний регистр
     * и сравниваем их с тем, что в форме
     *
     * @param $attr
     */
    public function uniqUsername($attr): void
    {
        /** @var User[] $users */
        $users = User::find()->all();

        foreach ($users as $user){
            if ($user->getId() !== $this->getId() && mb_strtolower($this->getUsername()) === mb_strtolower($user->getUsername())){
                $this->uniq_username = false;
                $this->addError($attr, 'Этот логин уже занят.');
            }
        }
    }


    /**
     * @param $attr
     */
    public function matchUsername($attr): void
    {
        if (!preg_match('/^[a-zA-Z0-9@._-]+$/', $this->username)) {
            $this->match_username = false;
            $this->addError($attr, 'Логин может содержать только латинские символы, цифры и специальные символы "@._-"');
        }

        if (preg_match('/\s+/',$this->username)) {
            $this->match_username = false;
            $this->addError($attr, 'Не допускается использование пробелов');
        }
    }


    /**
     * @param $attr
     */
    public function uniqEmail($attr): void
    {
        /** @var User[] $users */
        $users = User::find()->all();

        foreach ($users as $user){
            if ($user->getId() !== $this->getId() && $this->getEmail() === $user->getEmail()){
                $this->uniq_email = false;
                $this->addError($attr, 'Эта почта уже зарегистрирована.');
            }
        }
    }


    /**
     * Отправка уведомления на email
     * @return bool
     */
    public function sendEmail(): bool
    {
        try {

            $mail = Yii::$app->mailer->compose('changeProfile', ['user' => $this])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['siteName'] . ' - Акселератор стартап-проектов'])
                ->setTo($this->email)
                ->setSubject('Изменение профиля на сайте ' . Yii::$app->params['siteName']);

            $mail->send();
            return true;

        } catch (\Swift_TransportException  $e) {

            return  false;
        }
    }


    /**
     * @return $this|User|null
     */
    public function update()
    {
        $user = User::findOne($this->id);
        if ($user->getEmail() !== $this->getEmail()) {
            if ($this->sendEmail()) {
                $user->setEmail($this->email);
                $user->setUsername($this->username);

                return $user->save() ? $user : null;
            }

            $this->checking_mail_sending = false;
            return $this;
        }

        $user->setUsername($this->username);
        return $user->save() ? $user : null;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}


