<?php


namespace app\modules\expert\models\form;

use app\models\User;
use yii\base\Model;
use app\models\ExpertType;
use Yii;

/**
 * @property  int $id                                       id пользователя
 * @property  string $username                              Логин
 * @property  string $email                                 Эл. почта
 * @property  string $education                             Образование
 * @property  string $academic_degree                       Ученая степень
 * @property  string $position                              Должность
 * @property  array $type                                   Тип эксперта
 * @property  string $scope_professional_competence         Сфера профессиональной компетенции
 * @property  string $publications                          Научные публикации
 * @property  string $implemented_projects                  Реализованные проекты
 * @property  string $role_in_implemented_projects          Роль в реализованных проектах
 * @property  string $keywords                              Ключевые слова
 */
class ProfileExpertForm extends  Model
{

    /**
     * id пользователя
     * @var int
     */
    public $id;

    /**
     * Логин
     * @var string
     */
    public $username;

    /**
     * Эл. почта
     * @var string
     */
    public $email;

    /**
     * Образование
     * @var string
     */
    public $education;

    /**
     * Ученая степень
     * @var string
     */
    public $academic_degree;

    /**
     * Должность
     * @var string
     */
    public $position;

    /**
     * Тип эксперта
     * @var array
     */
    public $type;

    /**
     * Сфера профессиональной компетенции
     * @var string
     */
    public $scope_professional_competence;

    /**
     * Научные публикации
     * @var string
     */
    public $publications;

    /**
     * Реализованные проекты
     * @var string
     */
    public $implemented_projects;

    /**
     * Роль в реализованных проектах
     * @var string
     */
    public $role_in_implemented_projects;

    /**
     * Ключевые слова
     * @var string
     */
    public $keywords;

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
            [['username', 'email',
                'education', 'academic_degree', 'position', 'type', 'scope_professional_competence',
                'publications', 'implemented_projects', 'role_in_implemented_projects', 'keywords'], 'required'],
            [['username', 'email', 'education', 'academic_degree', 'position', 'scope_professional_competence',
                'publications', 'implemented_projects', 'role_in_implemented_projects', 'keywords'], 'trim'],
            [['email', 'education', 'academic_degree', 'position'], 'string', 'max' => 255],
            [['scope_professional_competence', 'publications', 'implemented_projects', 'role_in_implemented_projects', 'keywords'], 'string', 'max' => 2000],
            ['username', 'matchUsername'],
            ['username', 'uniqUsername'],
            ['email', 'uniqEmail'],
        ];
    }


    /**
     * ProfileForm constructor.
     *
     * @param int $id
     * @param array $config
     */
    public function __construct($id, array $config = [])
    {
        /** @var User $user */
        $user = User::find()->with(['expertInfo', 'keywords'])->andWhere(['id' => $id])->one();
        $this->keywords = $user->keywords->getDescription();
        foreach ($user as $key => $value) {
            if (property_exists($this, $key)) {
                $this[$key] = $value;
            }
        }
        foreach ($user->expertInfo as $key => $value) {
            if (property_exists($this, $key) && $key !== 'id') {
                if ($key === 'type') {
                    $this[$key] = ExpertType::getValue($value);
                } else {
                    $this[$key] = $value;
                }
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
            'education' => 'Образование',
            'academic_degree' => 'Ученая степень',
            'position' => 'Должность',
            'type' => 'Тип',
            'scope_professional_competence' => 'Сфера профессиональной компетенции',
            'publications' => 'Научные публикации',
            'implemented_projects' => 'Реализованные проекты',
            'role_in_implemented_projects' => 'Роль в реализованных проектах',
            'keywords' => 'Ключевые слова'
        ];
    }


    /**
     * Собственное правило для поля username
     * Переводим все логины в нижний регистр
     * и сравниваем их с тем, что в форме
     * @param $attr
     */
    public function uniqUsername($attr): void
    {
        /** @var User[] $users */
        $users = User::find()->all();

        foreach ($users as $user){
            if ($user->getId() !== $this->id && mb_strtolower($this->username) === mb_strtolower($user->getUsername())){
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
        $user = User::findOne($this->getId());

        if ($user->getEmail() !== $this->getEmail()) {
            if ($this->sendEmail()) {
                $user->setEmail($this->getEmail());
                $user->setUsername($this->getUsername());

                if ($user->save()) {

                    // Сохраняем ключевые слова
                    $user->keywords->edit($this->getKeywords());
                    // Сохраняем информацию о эксперте
                    $expertInfo = $user->expertInfo;
                    $expertInfo->setEducation($this->getEducation());
                    $expertInfo->setAcademicDegree($this->getAcademicDegree());
                    $expertInfo->setPosition($this->getPosition());
                    $expertInfo->setType(implode('|', $this->getType()));
                    $expertInfo->setScopeProfessionalCompetence($this->getScopeProfessionalCompetence());
                    $expertInfo->setPublications($this->getPublications());
                    $expertInfo->setImplementedProjects($this->getImplementedProjects());
                    $expertInfo->setRoleInImplementedProjects($this->getRoleInImplementedProjects());

                    if ($expertInfo->save()) {
                        return $user;
                    }
                }
            }

            $this->checking_mail_sending = false;
            return  $this;

        }

        $user->setUsername($this->getUsername());

        if ($user->save()) {

            // Сохраняем ключевые слова
            $user->keywords->edit($this->getKeywords());
            // Сохраняем информацию о эксперте
            $expertInfo = $user->expertInfo;
            $expertInfo->setEducation($this->getEducation());
            $expertInfo->setAcademicDegree($this->getAcademicDegree());
            $expertInfo->setPosition($this->getPosition());
            $expertInfo->setType(implode('|', $this->getType()));
            $expertInfo->setScopeProfessionalCompetence($this->getScopeProfessionalCompetence());
            $expertInfo->setPublications($this->getPublications());
            $expertInfo->setImplementedProjects($this->getImplementedProjects());
            $expertInfo->setRoleInImplementedProjects($this->getRoleInImplementedProjects());

            if ($expertInfo->save()) {
                return $user;
            }
        }

        return  $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
    public function getEducation(): string
    {
        return $this->education;
    }

    /**
     * @param string $education
     */
    public function setEducation(string $education): void
    {
        $this->education = $education;
    }

    /**
     * @return string
     */
    public function getAcademicDegree(): string
    {
        return $this->academic_degree;
    }

    /**
     * @param string $academic_degree
     */
    public function setAcademicDegree(string $academic_degree): void
    {
        $this->academic_degree = $academic_degree;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function getType(): array
    {
        return $this->type;
    }

    /**
     * @param array $type
     */
    public function setType(array $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getScopeProfessionalCompetence(): string
    {
        return $this->scope_professional_competence;
    }

    /**
     * @param string $scope_professional_competence
     */
    public function setScopeProfessionalCompetence(string $scope_professional_competence): void
    {
        $this->scope_professional_competence = $scope_professional_competence;
    }

    /**
     * @return string
     */
    public function getPublications(): string
    {
        return $this->publications;
    }

    /**
     * @param string $publications
     */
    public function setPublications(string $publications): void
    {
        $this->publications = $publications;
    }

    /**
     * @return string
     */
    public function getImplementedProjects(): string
    {
        return $this->implemented_projects;
    }

    /**
     * @param string $implemented_projects
     */
    public function setImplementedProjects(string $implemented_projects): void
    {
        $this->implemented_projects = $implemented_projects;
    }

    /**
     * @return string
     */
    public function getRoleInImplementedProjects(): string
    {
        return $this->role_in_implemented_projects;
    }

    /**
     * @param string $role_in_implemented_projects
     */
    public function setRoleInImplementedProjects(string $role_in_implemented_projects): void
    {
        $this->role_in_implemented_projects = $role_in_implemented_projects;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }
}
