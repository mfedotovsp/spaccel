<?php


namespace app\models;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * Класс для записи истории
 * доступа экспертов к проектам
 *
 * Class UserAccessToProjects
 * @package app\models
 *
 * @property int $id                                                    Идентификтор записи
 * @property int $user_id                                               Идентификтор эксперта
 * @property int $project_id                                            Идентификтор проекта
 * @property int $communication_id                                      Идентификтор коммуникации в таб. project_communications
 * @property int $communication_type                                    Тип коммуникации
 * @property int $cancel                                                Флаг, указывает на отмену коммуникации
 * @property int $date_stop                                             Дата окончания доступа к проекту
 * @property int $created_at                                            Дата создания записи
 * @property int $updated_at                                            Дата редактирования записи
 *
 * @property ProjectCommunications $communication                       Получить объект коммуникации по которой был предоставлен доступ к проекту
 * @property ProjectCommunications[] $userCommunications                Получить все коммуникации пользователя по проекту
 * @property ProjectCommunications[] $userCommunicationsForAdminTable   Получить коммуникации пользователя для таблицы админа
 * @property User $user                                                 Получить пользователя, которому был предоставлен доступ к проекту
 * @property Projects $project                                          Получить проект, по которому был предоставлен доступ
 */
class UserAccessToProjects extends ActiveRecord
{

    public const CANCEL_TRUE = 111;
    public const CANCEL_FALSE = 222;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_access_to_projects';
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
        return $this->hasOne(ProjectCommunications::class, ['id' => 'communication_id']);
    }


    /**
     * Получить все коммуникации
     * пользователя по проекту
     *
     * @return array|ActiveRecord[]
     */
    public function getUserCommunications(): array
    {
        $communications = ProjectCommunications::find()
            ->andWhere(['or', ['sender_id' => $this->getUserId()], ['adressee_id' => $this->getUserId()]])
            ->andWhere(['project_id' => $this->getProjectId()]);
         return $communications->all();
    }


    /**
     * Получить коммуникации пользователя
     * для таблицы админа
     *
     * @return array|ActiveRecord[]
     */
    public function getUserCommunicationsForAdminTable(): array
    {
        $communications = ProjectCommunications::find()
            ->andWhere(['or', ['sender_id' => $this->getUserId()], ['adressee_id' => $this->getUserId()]])
            ->andWhere(['project_id' => $this->getProjectId()])
            ->andWhere(['in', 'type', [
                CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE,
                CommunicationTypes::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE,
                CommunicationTypes::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE
            ]]);
        return $communications->all();
    }


    /**
     * Получить объект пользователя,
     * которому был предоставлен
     * доступ к проекту
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
            [['user_id', 'project_id', 'communication_id', 'communication_type', 'cancel', 'date_stop', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'project_id', 'communication_id', 'communication_type'], 'required'],
            ['cancel', 'default', 'value' => self::CANCEL_FALSE],
            ['cancel', 'in', 'range' => [
                self::CANCEL_FALSE,
                self::CANCEL_TRUE
            ]],
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }


    /**
     * @param int $user_id
     * @param int $project_id
     * @param ProjectCommunications $communication
     */
    public function setParams (int $user_id, int $project_id, ProjectCommunications $communication): void
    {
        $this->setUserId($user_id);
        $this->setProjectId($project_id);
        $this->setCommunicationId($communication->getId());
        $this->setCommunicationType($communication->getType());
        if ($this->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) {
            if ($communication->getPatternId()) {
                $pattern = CommunicationPatterns::findOne($communication->getPatternId());
                $this->setDateStop(time() + ($pattern->getProjectAccessPeriod() * 24 * 60 * 60));
            } else {
                $this->setDateStop(time() + (CommunicationPatterns::DEFAULT_USER_ACCESS_TO_PROJECT * 24 * 60 * 60));
            }
        }
    }


    /**
     * Установить парметр аннулированного
     * доступа к проекту
     */
    public function setCancel(): void
    {
        $this->cancel = self::CANCEL_TRUE;
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
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
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
     * @return int
     */
    public function getCancel(): int
    {
        return $this->cancel;
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

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

}