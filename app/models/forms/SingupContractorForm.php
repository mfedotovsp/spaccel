<?php


namespace app\models\forms;


use app\models\ContractorEducations;
use app\models\ContractorInfo;
use app\models\User;
use Yii;
use yii\base\Exception;

/**
 * Форма регистрации исполнителя
 *
 * Class SingupContractorForm
 * @package app\models\forms
 *
 * @property string $email
 * @property string $username
 * @property string $password
 * @property int $status
 * @property int $confirm
 * @property int $role
 * @property int $clientId
 * @property boolean $exist_experience
 * @property array $activities
 * @property string $academic_degree
 * @property string $position
 * @property string $publications
 * @property string $implemented_projects
 * @property string $role_in_implemented_projects
 */
class SingupContractorForm extends SingupForm
{
    /**
     * Признак опыта работы у исполнителя
     * @var bool
     */
    public $exist_experience = false;

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


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['exist_agree', 'uniq_username', 'uniq_email', 'exist_experience'],'boolean'],
            ['exist_agree', 'existAgree'],
            [['email', 'password', 'activities'], 'required'],
            ['clientId', 'safe'],
            [['email', 'password', 'academic_degree', 'position',
                'publications', 'implemented_projects', 'role_in_implemented_projects'], 'trim'],
            [['email', 'academic_degree', 'position'], 'string', 'max' => 255],
            [['publications', 'implemented_projects', 'role_in_implemented_projects'], 'string', 'max' => 2000],
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

            ['role', 'default', 'value' => User::ROLE_CONTRACTOR],
            ['role', 'in', 'range' => [User::ROLE_CONTRACTOR]],

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
            'exist_experience' => 'Имею опыт работы',
            'exist_agree' => '',
            'academic_degree' => 'Ученая степень',
            'position' => 'Должность',
            'activities' => 'Виды деятельности *',
            'publications' => 'Научные публикации',
            'implemented_projects' => 'Реализованные проекты',
            'role_in_implemented_projects' => 'Роль в реализованных проектах',
        ];
    }


    /**
     * @return User|false
     */
    public function singup()
    {
        if ($this->exist_agree == 1){

            $transaction = Yii::$app->db->beginTransaction();
            try {

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

                $arr_educations = $_POST['ContractorEducations'];
                $arr_educations = array_values($arr_educations);
                $countEducations = count($arr_educations);

                if ($countEducations > 0 && $user->save()) {

                    foreach ($arr_educations as $arr_education) {
                        $education = new ContractorEducations();
                        $education->setContractorId($user->getId());
                        $education->setEducationalInstitution($arr_education['educational_institution']);
                        $education->setFaculty($arr_education['faculty']);
                        $education->setCourse($arr_education['course'] ?: '');
                        $education->setFinishDate($arr_education['finish_date'] ?: '');
                        $education->save();
                    }

                    $numberEducations = (int)ContractorEducations::find()
                        ->andWhere(['contractor_id' => $user->getId()])
                        ->count();

                    if ($numberEducations !== $countEducations) {
                        $transaction->rollBack();
                        return false;
                    }

                    $contractorInfo = new ContractorInfo();
                    $contractorInfo->setContractorId($user->getId());
                    $contractorInfo->setActivities(implode('|', $this->getActivities()));
                    if ($this->isExistExperience()) {
                        $contractorInfo->setAcademicDegree($this->getAcademicDegree());
                        $contractorInfo->setPosition($this->getPosition());
                        $contractorInfo->setPublications($this->getPublications());
                        $contractorInfo->setImplementedProjects($this->getImplementedProjects());
                        $contractorInfo->setRoleInImplementedProjects($this->getRoleInImplementedProjects());
                    }

                    if ($contractorInfo->save()) {
                        $transaction->commit();
                        return $user;
                    }
                }

                $transaction->rollBack();
                return false;

            } catch (\Exception $exception) {
                $transaction->rollBack();
                return false;
            }
        }
        return false;
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
     * @return bool
     */
    public function isExistExperience(): bool
    {
        return $this->exist_experience;
    }

    /**
     * @param bool $exist_experience
     */
    public function setExistExperience(bool $exist_experience): void
    {
        $this->exist_experience = $exist_experience;
    }
}
