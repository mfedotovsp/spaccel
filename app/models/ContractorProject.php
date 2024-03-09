<?php

namespace app\models;

use Throwable;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\StaleObjectException;

/**
 * Класс, который хранит связи исполнителей с проектами
 *
 * Class ContractorProject
 * @package app\models
 *
 * @property int $contractor_id                 Идентификатор исполнителя
 * @property int $project_id                    Идентификатор проекта
 * @property int $activity_id                   Вид деятельности, по которому исполнитель был назначен на проект
 * @property int $created_at                    Дата создания связи
 * @property int|null $deleted_at               Дата удаления связи
 *
 * @property User $contractor                   Исполнитель проекта
 * @property Projects $project                  Проект, на который назначен исполнитель
 * @property ContractorActivities $activity     Вид деятельности исполнителя
 */
class ContractorProject extends ActiveRecord
{

    public const EVENT_REMOVE_RECORD = 'event remove record';
    public const EVENT_EDIT_RECORD = 'event edit record';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'project_id', 'activity_id'], 'required'],
            [['contractor_id', 'project_id', 'activity_id', 'created_at'], 'integer'],
            [['deleted_at'], 'safe'],
        ];
    }

    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->contractor->touch('updated_at');
            $this->project->touch('updated_at');
        });

        $this->on(self::EVENT_EDIT_RECORD, function (){
            $this->contractor->touch('updated_at');
            $this->project->touch('updated_at');
        });

        $this->on(self::EVENT_REMOVE_RECORD, function (){
            $this->contractor->touch('updated_at');
            $this->project->touch('updated_at');
        });

        parent::init();
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']]
            ],
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
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getActivity(): ActiveQuery
    {
        return $this->hasOne(ContractorActivities::class, ['id' => 'activity_id']);
    }

    /**
     * @return ContractorProject|null
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function create(): ?ContractorProject
    {
        $record = self::findOne([
            'contractor_id' => $this->getContractorId(),
            'project_id' => $this->getProjectId(),
            'activity_id' => $this->getActivityId(),
        ]);

        if (!$record) {
            return $this->save() ? $this : null;
        }

        $record->setDeletedAt(null);
        if ($record->update()) {
            $record->trigger(self::EVENT_EDIT_RECORD);
            return $record;
        }
        return null;
    }

    /**
     * @param int $contractorId
     * @param int $projectId
     * @param int $activityId
     * @return bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public static function remove(int $contractorId, int $projectId, int $activityId): bool
    {
        $record = self::findOne([
            'contractor_id' => $contractorId,
            'project_id' => $projectId,
            'activity_id' => $activityId,
        ]);

        $record->setDeletedAt(time());
        if ($record->update()) {
            $record->trigger(self::EVENT_REMOVE_RECORD);
            return true;
        }
        return false;
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
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }

    /**
     * @param int|null $deleted_at
     */
    public function setDeletedAt(?int $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @return int
     */
    public function getActivityId(): int
    {
        return $this->activity_id;
    }

    /**
     * @param int $activity_id
     */
    public function setActivityId(int $activity_id): void
    {
        $this->activity_id = $activity_id;
    }
}