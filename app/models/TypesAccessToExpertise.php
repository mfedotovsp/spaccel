<?php


namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит данные о типах экпертиз по проекту назначенные эксперту
 *
 * Class TypesAccessToExpertise
 * @property int id
 * @property int user_id
 * @property int project_id
 * @property int communication_id
 * @property string types
 *
 * @property ProjectCommunications $communication                   Коммуникация назначения на проект
 * @property Projects $project                                      Проект, на который был назначен эксперт
 * @property User $expert                                           Эксперт, назначенный на проект
 *
 * @package app\models
 */
class TypesAccessToExpertise extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'types_access_to_expertise';
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }


    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }


    /**
     * @param int $projectId
     */
    public function setProjectId(int $projectId): void
    {
        $this->project_id = $projectId;
    }


    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->project_id;
    }


    /**
     * @param int $communicationId
     */
    public function setCommunicationId(int $communicationId): void
    {
        $this->communication_id = $communicationId;
    }


    /**
     * @return int
     */
    public function getCommunicationId(): int
    {
        return $this->communication_id;
    }


    /**
     * @param array $arrayTypes
     */
    public function setTypes(array $arrayTypes): void
    {
        $this->types = implode('|', $arrayTypes);
    }


    /**
     * @return string
     */
    public function getTypes(): string
    {
        return $this->types;
    }


    /**
     * Получить объект коммуникации
     * назначения на проект
     *
     * @return ActiveQuery
     */
    public function getCommunication(): ActiveQuery
    {
        return $this->hasOne(ProjectCommunications::class, ['id' => 'communication_id']);
    }


    /**
     * Получить объект проекта,
     * на который был назначен эксперт
     *
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * Получить объект эксперта
     * назнасченного на проект
     *
     * @return ActiveQuery
     */
    public function getExpert(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['user_id', 'project_id', 'communication_id'], 'integer'],
            [['user_id', 'project_id', 'communication_id', 'types'], 'required'],
            [['types'], 'string', 'max' => 255],
        ];
    }


    /**
     * Сохранить объект в бд
     *
     * @param int $userId
     * @param int $projectId
     * @param int $communicationId
     * @param array $arrayTypes
     * @return TypesAccessToExpertise|null
     */
    public function create(int $userId, int $projectId, int $communicationId, array $arrayTypes): ?TypesAccessToExpertise
    {
        $this->setUserId($userId);
        $this->setProjectId($projectId);
        $this->setCommunicationId($communicationId);
        $this->setTypes($arrayTypes);
        return $this->save() ? $this : null;
    }
}