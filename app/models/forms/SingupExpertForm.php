<?php


namespace app\models\forms;


use app\models\ExpertInfo;
use app\models\KeywordsExpert;
use app\models\User;
use yii\base\Exception;

/**
 * Форма регистрации эксперта
 *
 * Class SingupExpertForm
 * @package app\models\forms
 *
 * @property string $email
 * @property string $username
 * @property string $password
 * @property int $status
 * @property int $confirm
 * @property int $role
 * @property int $clientId
 * @property string $education
 * @property string $academic_degree
 * @property string $position
 * @property array $type
 * @property string $scope_professional_competence
 * @property string $publications
 * @property string $implemented_projects
 * @property string $role_in_implemented_projects
 * @property string $keywords
 */
class SingupExpertForm extends SingupForm
{

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


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['exist_agree', 'uniq_username', 'uniq_email'],'boolean'],
            ['exist_agree', 'existAgree'],
            [['email', 'password',
                'education', 'academic_degree', 'position', 'type', 'scope_professional_competence',
                'publications', 'implemented_projects', 'role_in_implemented_projects', 'keywords'], 'required'],
            ['clientId', 'safe'],
            [['email', 'password',
                'education', 'academic_degree', 'position', 'scope_professional_competence',
                'publications', 'implemented_projects', 'role_in_implemented_projects', 'keywords'], 'trim'],
            [['email', 'education', 'academic_degree', 'position'], 'string', 'max' => 255],
            [['scope_professional_competence', 'publications', 'implemented_projects', 'role_in_implemented_projects', 'keywords'], 'string', 'max' => 2000],
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

            ['role', 'default', 'value' => User::ROLE_EXPERT],
            ['role', 'in', 'range' => [User::ROLE_EXPERT]],

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
            'exist_agree' => '',
            'education' => 'Образование *',
            'academic_degree' => 'Ученая степень *',
            'position' => 'Должность *',
            'type' => 'Тип *',
            'scope_professional_competence' => 'Сфера профессиональной компетенции *',
            'publications' => 'Научные публикации *',
            'implemented_projects' => 'Реализованные проекты *',
            'role_in_implemented_projects' => 'Роль в реализованных проектах *',
            'keywords' => 'Ключевые слова *'
        ];
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

            if ($user->save()) {

                // Сохраняем ключевые слова
                KeywordsExpert::create($user->getId(), $this->getKeywords());

                // Сохраняем информацию о эксперте
                $expertInfo = new ExpertInfo();
                $expertInfo->setUserId($user->getId());
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
        return false;
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