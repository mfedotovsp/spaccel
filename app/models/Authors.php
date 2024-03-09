<?php

namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит информацию об авторах проектов
 *
 * Class Authors
 * @package app\models
 *
 * @property int $id                            Идентификатор записи
 * @property int $project_id                    Идентификатор записи из таб.Projects
 * @property string $fio                        ФИО автора проекта
 * @property string $role                       Роль автора в проекте
 * @property string $experience                 Опыт работы автора проекта
 * @property int|null $deleted_at               Дата удаления
 *
 * @property Projects $project                  Проект
 */
class Authors extends ActiveRecord
{
    use SoftDeleteModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'authors';
    }


    /**
     * Получить объект проекта
     *
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['fio', 'role'], 'required'],
            [['project_id'], 'integer'],
            [['experience'], 'string', 'max' => 2000],
            [['fio', 'role'], 'string', 'max' => 255],
            [['fio', 'role', 'experience'], 'trim'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'fio' => 'Фамилия, имя, отчество автора проекта',
            'role' => 'Роль в проекте',
            'experience' => 'Опыт работы',
        ];
    }


    public function init()
    {

        $this->on(self::EVENT_AFTER_DELETE, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        parent::init();
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
    public function getProjectId(): int
    {
        return $this->project_id;
    }

    /**
     * @param int $project_id
     */
    public function setProjectId(int $project_id): void
    {
        $this->project_id = $project_id;
    }

    /**
     * @return string
     */
    public function getFio(): string
    {
        return $this->fio;
    }

    /**
     * @param string $fio
     */
    public function setFio(string $fio): void
    {
        $this->fio = $fio;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getExperience(): string
    {
        return $this->experience;
    }

    /**
     * @param string $experience
     */
    public function setExperience(string $experience): void
    {
        $this->experience = $experience;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }
}
