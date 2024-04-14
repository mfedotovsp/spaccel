<?php

namespace app\modules\contractor\models\form;

use app\models\User;
use app\services\MailerService;
use Yii;
use yii\base\Model;

/**
 * @property int $id                                        id пользователя
 * @property string $email                                  Эл. почта
 * @property string $username                               Логин
 * @property string $educational_institution                Учебное заведение
 * @property string $faculty                                Факультет
 * @property string $course                                 Курс
 * @property int $finish_date                               Дата окончания
 * @property array $activities                              Виды деятельности
 * @property string $academic_degree                        Ученая степень
 * @property string $position                               Должность
 * @property string $publications                           Научные публикации
 * @property string $implemented_projects                   Реализованные проекты
 * @property string $role_in_implemented_projects           Роль в реализованных проектах
 */
class ProfileContractorForm extends  Model
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
     * Учебное заведение
     * @var string
     */
    public $educational_institution;

    /**
     * Факультет
     * @var string
     */
    public $faculty;

    /**
     * Курс
     * @var string
     */
    public $course;

    /**
     * Дата окончания
     * @var int|string|null
     */
    public $finish_date;

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
     * Виды деятельности
     * @var array
     */
    public $activities;

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
            [['username', 'email', 'activities', 'educational_institution', 'faculty'], 'required'],
            [['username', 'email', 'educational_institution', 'faculty', 'course', 'academic_degree',
                'position', 'publications', 'implemented_projects', 'role_in_implemented_projects'], 'trim'],
            [['username', 'email', 'educational_institution', 'faculty', 'course', 'academic_degree',
                'position'], 'string', 'max' => 255],
            [['publications', 'implemented_projects', 'role_in_implemented_projects'], 'string', 'max' => 2000],
            [['finish_date'], 'safe'],
            ['username', 'matchUsername'],
            ['username', 'uniqUsername'],
            ['email', 'uniqEmail'],
        ];
    }

    /**
     * ProfileContractorForm constructor.
     *
     * @param int $id
     * @param array $config
     */
    public function __construct($id, array $config = [])
    {
        /** @var User $user */
        $user = User::find()->with(['contractorEducations', 'contractorInfo'])
            ->andWhere(['id' => $id])
            ->one();

        foreach ($user as $key => $value) {
            if (property_exists($this, $key)) {
                $this[$key] = $value;
            }
        }

        foreach ($user->contractorInfo as $key => $value) {
            if (property_exists($this, $key) && $key !== 'id') {
                if ($key === 'activities') {
                    $value = explode('|', $value);
                }
                $this[$key] = $value;
            }
        }

        foreach ($user->contractorEducations[0] as $key => $value) {
            if (property_exists($this, $key) && $key !== 'id') {
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
            'email' => 'Email *',
            'username' => 'Логин *',
            'activities' => 'Виды деятельности *',
            'educational_institution' => 'Учебное заведение *',
            'faculty' => 'Факультет *',
            'course' => 'Курс',
            'finish_date' => 'Дата окончания',
            'academic_degree' => 'Ученая степень',
            'position' => 'Должность',
            'publications' => 'Научные публикации',
            'implemented_projects' => 'Реализованные проекты',
            'role_in_implemented_projects' => 'Роль в реализованных проектах',
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
        return MailerService::send(
            $this->getEmail(),
            'Изменение профиля на сайте ' . Yii::$app->params['siteName'],
            'changeProfile',
            ['user' => $this]
        );
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

                    // Сохраняем образование исполнителя
                    $contractorEducation = $user->contractorEducations[0];
                    $contractorEducation->setEducationalInstitution($this->getEducationalInstitution());
                    $contractorEducation->setFaculty($this->getFaculty());
                    $contractorEducation->setCourse($this->getCourse());
                    $contractorEducation->setFinishDate($this->getFinishDate());

                    // Сохраняем информацию о исполнителе
                    $contractorInfo = $user->contractorInfo;
                    $contractorInfo->setActivities(implode('|', $this->getActivities()));
                    $contractorInfo->setAcademicDegree($this->getAcademicDegree());
                    $contractorInfo->setPosition($this->getPosition());
                    $contractorInfo->setPublications($this->getPublications());
                    $contractorInfo->setImplementedProjects($this->getImplementedProjects());
                    $contractorInfo->setRoleInImplementedProjects($this->getRoleInImplementedProjects());

                    if ($contractorEducation->save() && $contractorInfo->save()) {
                        return $user;
                    }
                }
            }

            $this->checking_mail_sending = false;
            return  $this;

        }

        $user->setUsername($this->getUsername());

        if ($user->save()) {

            // Сохраняем образование исполнителя
            $contractorEducation = $user->contractorEducations[0];
            $contractorEducation->setEducationalInstitution($this->getEducationalInstitution());
            $contractorEducation->setFaculty($this->getFaculty());
            $contractorEducation->setCourse($this->getCourse());
            $contractorEducation->setFinishDate($this->getFinishDate());

            // Сохраняем информацию о исполнителе
            $contractorInfo = $user->contractorInfo;
            $contractorInfo->setActivities(implode('|', $this->getActivities()));
            $contractorInfo->setAcademicDegree($this->getAcademicDegree());
            $contractorInfo->setPosition($this->getPosition());
            $contractorInfo->setPublications($this->getPublications());
            $contractorInfo->setImplementedProjects($this->getImplementedProjects());
            $contractorInfo->setRoleInImplementedProjects($this->getRoleInImplementedProjects());

            if ($contractorEducation->save() && $contractorInfo->save()) {
                return $user;
            }
        }

        return $this;
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
     * @return string
     */
    public function getEducationalInstitution(): string
    {
        return $this->educational_institution;
    }

    /**
     * @param string $educational_institution
     */
    public function setEducationalInstitution(string $educational_institution): void
    {
        $this->educational_institution = $educational_institution;
    }

    /**
     * @return string
     */
    public function getFaculty(): string
    {
        return $this->faculty;
    }

    /**
     * @param string $faculty
     */
    public function setFaculty(string $faculty): void
    {
        $this->faculty = $faculty;
    }

    /**
     * @return string
     */
    public function getCourse(): string
    {
        return $this->course;
    }

    /**
     * @param string $course
     */
    public function setCourse(string $course): void
    {
        $this->course = $course;
    }

    /**
     * @return int|string|null
     */
    public function getFinishDate()
    {
        return $this->finish_date;
    }

    /**
     * @param int $finish_date
     */
    public function setFinishDate(int $finish_date): void
    {
        $this->finish_date = $finish_date;
    }

    /**
     * @return array
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * @param array $activities
     */
    public function setActivities(array $activities): void
    {
        $this->activities = $activities;
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
}
