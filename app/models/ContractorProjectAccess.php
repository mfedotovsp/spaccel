<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Класс для записи истории
 * доступа исполнителей к проектам
 *
 * Class ContractorProjectAccess
 * @package app\models
 *
 * @property int $id                                                    Идентификтор записи
 * @property int $contractor_id                                         Идентификтор исполнителя
 * @property int $project_id                                            Идентификтор проекта
 * @property int $communication_id                                      Идентификтор коммуникации в таб. contractor_communications
 * @property int $communication_type                                    Тип коммуникации
 * @property int|null $stage                                            Этап проекта
 * @property int|null $stage_id                                         Идентификатор этапа проекта
 * @property int $date_stop                                             Дата окончания доступа к проекту
 * @property int $created_at                                            Дата создания записи
 *
 * @property ContractorCommunications $communication                    Получить объект коммуникации по которой был предоставлен доступ к проекту
 * @property ContractorCommunications[] $userCommunications             Получить все коммуникации исполнителя по проекту
 * @property User $contractor                                           Получить пользователя, которому был предоставлен доступ к проекту
 * @property Projects $project                                          Получить проект, по которому был предоставлен доступ
 */
class ContractorProjectAccess extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'contractor_project_access';
    }

    /**
     * Получить объект коммуникации
     * по которой был предоставлен
     * доступ к проекту
     *
     * @return ActiveQuery
     */
    public function getCommunication(): ActiveQuery
    {
        return $this->hasOne(ContractorCommunications::class, ['id' => 'communication_id']);
    }

    /**
     * Получить все коммуникации
     * пользователя по проекту
     *
     * @return array|ActiveRecord[]
     */
    public function getContractorCommunications(): array
    {
        $communications = ProjectCommunications::find()
            ->andWhere(['or', ['sender_id' => $this->getContractorId()], ['adressee_id' => $this->getContractorId()]])
            ->andWhere(['project_id' => $this->getProjectId()]);
        return $communications->all();
    }


    /**
     * Существует ли у исполнителя доступ к проекту и этапам проекта
     *
     * @param int $contractorId
     * @param int $projectId
     * @param int|null $stage
     * @param int|null $stageId
     * @return bool
     */
    public static function existAccessByParams(int $contractorId, int $projectId, int $stage = null, int $stageId = null): bool
    {
        /** @var $contractorProject ContractorProject */
        $contractorProject = ContractorProject::find()
            ->andWhere([
                'contractor_id' => $contractorId,
                'project_id' => $projectId,
                'deleted_at' => null
            ])->orderBy(['created_at' => SORT_ASC])
            ->one();

        $lastRecordQuery = self::find()->andWhere(['contractor_id' => $contractorId, 'project_id' => $projectId]);
        if ($stage && $stageId) {
            $lastRecordQuery = $lastRecordQuery->andWhere(['stage' => $stage, 'stage_id' => $stageId]);
            /** @var $lastRecord ContractorProjectAccess|null */
            $lastRecord = $lastRecordQuery->orderBy(['created_at' => SORT_DESC])->one();
            if ($lastRecord && $contractorProject) {
                return true;
            }

        } else {
            /** @var $lastRecord ContractorProjectAccess|null */
            $lastRecord = $lastRecordQuery->orderBy(['created_at' => SORT_DESC])->one();
            if (!$lastRecord) {
                return false;
            }

            if ($lastRecord->getCommunicationType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT &&
                time() < $lastRecord->getDateStop()) {
                return true;
            }

            if ($lastRecord->getCommunicationType() === ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT &&
                $lastRecord->communication->communicationResponse->getAnswer() === ContractorCommunicationResponse::POSITIVE_RESPONSE &&
                time() < $lastRecord->communication->communicationAnswered->contractorProjectAccess->getDateStop()) {
                return true;
            }

            if ($contractorProject && $lastRecord->getCommunicationType() === ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT) {
                return true;
            }
        }

        return false;
    }


    /**
     * Получить объект исполнителя,
     * которому был предоставлен
     * доступ к проекту
     *
     * @return ActiveQuery
     */
    public function getContractor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
    }


    /**
     * Получить объект проекта
     * по которому был предоставлен доступ
     *
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'project_id', 'communication_id', 'communication_type', 'created_at'], 'integer'],
            [['stage', 'stage_id', 'date_stop'], 'safe'],
            [['contractor_id', 'project_id', 'communication_id', 'communication_type'], 'required'],
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
        ];
    }


    /**
     * @param int $user_id
     * @param int $project_id
     * @param ContractorCommunications $communication
     */
    public function setParams (int $user_id, int $project_id, ContractorCommunications $communication): void
    {
        $this->setContractorId($user_id);
        $this->setProjectId($project_id);
        $this->setCommunicationId($communication->getId());
        $this->setCommunicationType($communication->getType());
        $this->setStage($communication->getStage());
        $this->setStageId($communication->getStageId());
        if ($this->getCommunicationType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT) {
            $this->setDateStop(time() + (ContractorCommunicationPatterns::DEFAULT_CONTRACTOR_ACCESS_TO_PROJECT * 24 * 60 * 60));
        }
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
    public function getCommunicationId(): int
    {
        return $this->communication_id;
    }

    /**
     * @param int $communication_id
     */
    public function setCommunicationId(int $communication_id): void
    {
        $this->communication_id = $communication_id;
    }

    /**
     * @return int
     */
    public function getCommunicationType(): int
    {
        return $this->communication_type;
    }

    /**
     * @param int $communication_type
     */
    public function setCommunicationType(int $communication_type): void
    {
        $this->communication_type = $communication_type;
    }

    /**
     * @return int|null
     */
    public function getStage(): ?int
    {
        return $this->stage;
    }

    /**
     * @param int|null $stage
     */
    public function setStage(?int $stage): void
    {
        $this->stage = $stage;
    }

    /**
     * @return int|null
     */
    public function getStageId(): ?int
    {
        return $this->stage_id;
    }

    /**
     * @param int|null $stage_id
     */
    public function setStageId(?int $stage_id): void
    {
        $this->stage_id = $stage_id;
    }

    /**
     * @return int
     */
    public function getDateStop(): int
    {
        return $this->date_stop;
    }

    /**
     * @param int $date_stop
     */
    public function setDateStop(int $date_stop): void
    {
        $this->date_stop = $date_stop;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }
}