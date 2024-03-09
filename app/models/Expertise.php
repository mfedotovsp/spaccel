<?php


namespace app\models;


use app\models\forms\expertise\FormExpertiseManyAnswer;
use app\models\interfaces\ConfirmationInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * Класс, который хранит данные о экспертизах, проведенные экспертами
 *
 * Class Expertise
 * @package app\models
 *
 * @property int $id                    Идентификатор экспертизы
 * @property int $stage                 Этап проекта от 0 до 9, по которому проведена экспертиза
 * @property int $stage_id              Идентификатор этапа проекта, по которому проведена экспертиза
 * @property int $expert_id             Идентификатор эксперта
 * @property int $user_id               Идентификатор проектанта проекта, по которому проходит экспертиза
 * @property int $type_expert           Тип деятельности эксперта, по которому была проведена экспертиза
 * @property string $estimation            Оценка (оценки) выставленная экспертом
 * @property string $comment               Комментарий(рекомендации) эксперта
 * @property int $communication_id      Идентификатор коммуникации из таблицы project_communications, по которой был дан доступ к экспертизе
 * @property int $completed             Параметр завершенности экспертизы, если экспертизы завершена, то её могут видеть другие пользователи. Если экспертиза завершена, то будут отправлены коммуникации (уведомления) проектанту и трекеру
 * @property int $created_at            Время создания экспертизы
 * @property int $updated_at            Время обновления экспертизы
 *
 * @property User $user                 Проектант
 * @property User $expert               Эксперт
 */
class Expertise extends ActiveRecord
{

    public const COMPLETED = 1001;
    public const NO_COMPLETED = 1010;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'expertise';
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Получить этап, на котором
     * проходит экспертиза
     *
     * @return int
     */
    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * Установить этап, на котором
     * проходит экспертиза
     *
     * @param int $stage
     */
    public function setStage(int $stage): void
    {
        $this->stage = $stage;
    }

    /**
     * Получить ID этапа, на котором
     * проходит экспертиза
     *
     * @return int
     */
    public function getStageId(): int
    {
        return $this->stage_id;
    }

    /**
     * Установить ID этапа, на котором
     * проходит экспертиза
     *
     * @param int $stageId
     */
    public function setStageId(int $stageId): void
    {
        $this->stage_id = $stageId;
    }

    /**
     * Получить ID User эксперта
     *
     * @return int
     */
    public function getExpertId(): int
    {
        return $this->expert_id;
    }

    /**
     * Установить ID User эксперта
     *
     * @param int $expertId
     */
    public function setExpertId(int $expertId): void
    {
        $this->expert_id = $expertId;
    }

    /**
     * Получить ID User проектанта
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Установить ID User проектанта
     *
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    /**
     * Получить тип экспертной деятельности
     *
     * @return int
     */
    public function getTypeExpert(): int
    {
        return $this->type_expert;
    }

    /**
     * Установить тип экспертной деятельности
     *
     * @param int $typeExpert
     */
    public function setTypeExpert(int $typeExpert): void
    {
        $this->type_expert = $typeExpert;
    }

    /**
     * Получить оценку эксперта
     *
     * @return string
     */
    public function getEstimation(): string
    {
        return $this->estimation;
    }

    /**
     * Установить оценку эксперта
     *
     * @param string $estimation
     */
    public function setEstimation(string $estimation): void
    {
        $this->estimation = $estimation;
    }


    /**
     * Получить общее количество баллов по всем вопросам
     * по одной экспертизе одного эксперта
     *
     * @return int|string
     */
    public function getGeneralEstimationByOne()
    {
        $stageClass = StageExpertise::getClassByStage(StageExpertise::getList()[$this->getStage()]);
        $interfaces = class_implements($stageClass);
        if (!isset($interfaces[ConfirmationInterface::class])) {
            return $this->getEstimation();
        }
        return FormExpertiseManyAnswer::getGeneralEstimationByOne($this->getEstimation());
    }

    /**
     * Получить комментарий эксперта
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Установить уомментарий эксперта
     *
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * Получить ID коммуникации
     *
     * @return int
     */
    public function getCommunicationId(): int
    {
        return $this->communication_id;
    }

    /**
     * Установить ID коммуникации
     *
     * @param int $communicationId
     */
    public function setCommunicationId(int $communicationId): void
    {
        $this->communication_id = $communicationId;
    }

    /**
     * Получить параметр
     * завершения экспертизы
     *
     * @return int
     */
    public function getCompleted(): int
    {
        return $this->completed;
    }

    /**
     * Установить параметр
     * завершения экспертизы
     */
    public function setCompleted(): void
    {
        $this->completed = self::COMPLETED;
    }


    /**
     * Получение объекта коммуникации, по которой была назначена экспертиза
     * @return ProjectCommunications|null
     */
    public function findProjectCommunication(): ?ProjectCommunications
    {
        return ProjectCommunications::findOne($this->getCommunicationId());
    }


    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getExpert(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'expert_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['stage', 'stage_id', 'expert_id', 'user_id', 'type_expert', 'estimation', 'comment', 'communication_id'], 'required'],
            [['stage', 'stage_id', 'expert_id', 'user_id', 'type_expert', 'communication_id', 'created_at', 'updated_at'], 'integer'],
            ['stage', 'in', 'range' => array_keys(StageExpertise::getList())],
            [['estimation'], 'string'],
            [['comment'], 'string', 'max' => 2000],
            ['completed', 'default', 'value' => self::NO_COMPLETED],
            ['completed', 'in', 'range' => [
                self::NO_COMPLETED,
                self::COMPLETED
            ]],
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }


    /**
     * Проверка на наличие и завершенность экспертизы
     *
     * @param int $stage
     * @param int $stageId
     * @param int $type
     * @param int $expertId
     * @return int|null
     */
    public static function checkExistAndCheckCompleted(int $stage, int $stageId, int $type, int $expertId): ?int
    {
        $expertise = self::findOne([
            'stage' => $stage,
            'stage_id' => $stageId,
            'type_expert' => $type,
            'expert_id' => $expertId
        ]);

        return $expertise->completed ?? null;
    }


    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes): void
    {
        if ($insert && $this->getCompleted() === self::COMPLETED) {
            // Отправка уведомления о завершении экспетизы при ее создании
            $this->sendCommunications();
        } elseif (!$insert) {
            if (isset($changedAttributes['completed']) && $changedAttributes['completed'] !== $this->getCompleted() && $this->getCompleted() === self::COMPLETED) {
                // Отправка уведомления о завершении экспетизы при ее обновлении
                $this->sendCommunications();
            } elseif (!isset($changedAttributes['completed']) && ((isset($changedAttributes['estimation']) || isset($changedAttributes['comment'])) || (isset($changedAttributes['estimation']) && isset($changedAttributes['comment']))) && $this->getCompleted() == self::COMPLETED) {
                // Отправка уведомления об обновлении данных ранее завершенной экспертизы
                $this->sendCommunications(true);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }


    /**
     * Сохранение экспертизы
     *
     * @return bool
     */
    public function saveRecord(): bool
    {
        return $this->validate() && $this->save();
    }


    /**
     * Отправка коммуникаций по экспертизе трекеру и проектанту
     *
     * @param bool $update
     */
    private function sendCommunications(bool $update = false): void
    {
        $communication = $this->findProjectCommunication();
        if (!$update) {
            // Отправить коммуникацию о завершении экспертизы
            DuplicateCommunications::create($communication, $this->user, TypesDuplicateCommunication::EXPERT_COMPLETED_EXPERTISE, $this);
            DuplicateCommunications::create($communication, $this->user->admin, TypesDuplicateCommunication::EXPERT_COMPLETED_EXPERTISE, $this);
        }else {
            // Отправить коммуникацию об обновлении данных ранее завершенной экспертизы
            DuplicateCommunications::create($communication, $this->user, TypesDuplicateCommunication::EXPERT_UPDATE_DATA_COMPLETED_EXPERTISE, $this);
            DuplicateCommunications::create($communication, $this->user->admin, TypesDuplicateCommunication::EXPERT_UPDATE_DATA_COMPLETED_EXPERTISE, $this);
        }
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