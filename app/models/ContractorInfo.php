<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит информацию о создании исполнителей проектов
 *
 * Class ContractorInfo
 * @package app\models
 *
 * @property int $id                                            Идентификатор записи
 * @property int $contractor_id                                 Идентификатор исполнителя проекта
 * @property string|array $activities                           Виды деятельности
 * @property string|null $academic_degree                       Ученая степень исполнителя
 * @property string|null $position                              Должность исполнителя
 * @property string|null $publications                          Научные публикации исполнителя
 * @property string|null $implemented_projects                  Реализованные проекты исполнителя
 * @property string|null $role_in_implemented_projects          Роль исполнителя в реализованных проектах
 *
 * @property User $contractor                                   Исполнитель
 * @property ContractorActivities[] $contractorActivities       Виды деятельности исполнителя
 */
class ContractorInfo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_info';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'activities'], 'required'],
            [['academic_degree', 'position', 'publications', 'implemented_projects', 'role_in_implemented_projects'], 'trim'],
            [['academic_degree', 'position', 'activities'], 'string', 'max' => 255],
            [['publications', 'implemented_projects', 'role_in_implemented_projects'], 'string', 'max' => 2000],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'activities' => 'Виды деятельности',
            'academic_degree' => 'Ученая степень',
            'position' => 'Должность',
            'publications' => 'Научные публикации',
            'implemented_projects' => 'Реализованные проекты',
            'role_in_implemented_projects' => 'Роль в реализованных проектах'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContractor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
    }

    /**
     * @return ContractorActivities[]
     */
    public function getContractorActivities(): array
    {
        $ids = explode('|', $this->getActivities());
        return ContractorActivities::findAll(['id' => $ids]);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getContractorId(): int
    {
        return $this->contractor_id;
    }

    /**
     * @param int $contractor_id
     */
    public function setContractorId(int $contractor_id): void
    {
        $this->contractor_id = $contractor_id;
    }

    /**
     * @return array|string
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @param array|string $activities
     */
    public function setActivities($activities): void
    {
        $this->activities = $activities;
    }

    /**
     * @return string
     */
    public function getAcademicDegree(): string
    {
        return $this->academic_degree ?: '';
    }

    /**
     * @param string|null $academic_degree
     */
    public function setAcademicDegree(?string $academic_degree): void
    {
        $this->academic_degree = $academic_degree;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position ?: '';
    }

    /**
     * @param string|null $position
     */
    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getPublications(): string
    {
        return $this->publications ?: '';
    }

    /**
     * @param string|null $publications
     */
    public function setPublications(?string $publications): void
    {
        $this->publications = $publications;
    }

    /**
     * @return string
     */
    public function getImplementedProjects(): string
    {
        return $this->implemented_projects ?: '';
    }

    /**
     * @param string|null $implemented_projects
     */
    public function setImplementedProjects(?string $implemented_projects): void
    {
        $this->implemented_projects = $implemented_projects;
    }

    /**
     * @return string
     */
    public function getRoleInImplementedProjects(): string
    {
        return $this->role_in_implemented_projects ?: '';
    }

    /**
     * @param string|null $role_in_implemented_projects
     */
    public function setRoleInImplementedProjects(?string $role_in_implemented_projects): void
    {
        $this->role_in_implemented_projects = $role_in_implemented_projects;
    }
}