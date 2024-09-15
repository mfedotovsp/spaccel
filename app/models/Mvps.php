<?php

namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;
use yii\helpers\FileHelper;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит объекты mvp-продуктов в бд
 *
 * Class Mvps
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. mvps
 * @property int $basic_confirm_id                  Идентификатор записи в таб. confirm_gcp
 * @property int $segment_id                        Идентификатор записи в таб. segments
 * @property int $project_id                        Идентификатор записи в таб. projects
 * @property int $problem_id                        Идентификатор записи в таб. problems
 * @property int $gcp_id                            Идентификатор записи в таб. gcps
 * @property string $title                          Сформированное системой название mvp-продукта
 * @property string $description                    Описание mvp-продукта
 * @property int $created_at                        Дата создания mvp-продукта
 * @property int $updated_at                        Дата обновления mvp-продукта
 * @property int $time_confirm                      Дата подверждения mvp-продукта
 * @property int $exist_confirm                     Параметр факта подтверждения mvp-продукта
 * @property string $enable_expertise               Параметр разрешения на экспертизу по даному этапу
 * @property int|null $enable_expertise_at          Дата разрешения на экспертизу по даному этапу
 * @property int|null $deleted_at                   Дата удаления
 * @property int|null $contractor_id                Идентификатор исполнителя, создавшего MVP (если null - MVP создан проектантом)
 * @property int|null $task_id                      Идентификатор задания исполнителя, по которому создан MVP (если null - MVP создан проектантом)
 * @property PropertyContainer $propertyContainer   Свойство для реализации шаблона 'контейнер свойств'
 *
 * @property ConfirmMvp $confirm                    Подтверждение mvp-продукта
 * @property Projects $project                      Проект
 * @property Segments $segment                      Сегмент
 * @property Problems $problem                      Проблема
 * @property Gcps $gcp                              Ценностное предложение
 * @property BusinessModel $businessModel           Бизнес-модель
 * @property RespondsGcp[] $respondsAgents          Респонденты которые подтвердили текущее ценностное предложение
 * @property User|null $contractor                  Исполнитель создавший MVP
 */
class Mvps extends ActiveRecord
{
    use SoftDeleteModelTrait;

    public const EVENT_CLICK_BUTTON_CONFIRM = 'event click button confirm';

    public $propertyContainer;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'mvps';
    }


    /**
     * Mvps constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setPropertyContainer();
        parent::__construct($config);
    }


    /**
     * Получить объект подтверждения данного Mvps
     *
     * @return ActiveQuery
     */
    public function getConfirm(): ActiveQuery
    {
        return $this->hasOne(ConfirmMvp::class, ['mvp_id' => 'id']);
    }


    /**
     * @return int
     */
    public function getConfirmGcpId(): int
    {
        return $this->basic_confirm_id;
    }


    /**
     * Получить объект текущего проекта
     *
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * Получить объект текущего сегмента
     *
     * @return ActiveQuery
     */
    public function getSegment(): ActiveQuery
    {
        return $this->hasOne(Segments::class, ['id' => 'segment_id']);
    }


    /**
     * Получить объект текущей проблемы
     *
     * @return ActiveQuery
     */
    public function getProblem(): ActiveQuery
    {
        return $this->hasOne(Problems::class, ['id' => 'problem_id']);
    }


    /**
     * Получить объект текущего Gcps
     *
     * @return ActiveQuery
     */
    public function getGcp(): ActiveQuery
    {
        return $this->hasOne(Gcps::class, ['id' => 'gcp_id']);
    }


    /**
     * Получить объект бизнес-модели
     *
     * @return ActiveQuery
     */
    public function getBusinessModel(): ActiveQuery
    {
        return $this->hasOne(BusinessModel::class, ['mvp_id' => 'id']);
    }


    /**
     * Получить респондентов, которые
     * подтвердтлт текущее Gcps
     *
     * @return array|ActiveRecord[]
     */
    public function getRespondsAgents(): array
    {
        return RespondsGcp::find()->with('interview')
            ->leftJoin('interview_confirm_gcp', '`interview_confirm_gcp`.`respond_id` = `responds_gcp`.`id`')
            ->andWhere(['confirm_id' => $this->getConfirmGcpId(), 'interview_confirm_gcp.status' => '1'])
            ->andWhere(['contractor_id' => null])
            ->all();
    }


    /**
     * Исполнитель создавший MVP
     * @return User|null
     */
    public function getContractor(): ?User
    {
        return $this->getContractorId() ? User::findOne($this->getContractorId()) : null;
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['basic_confirm_id', 'title', 'description'], 'required'],
            [['title', 'description'], 'trim'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 2000],
            [['time_confirm', 'basic_confirm_id', 'exist_confirm', 'project_id', 'segment_id', 'problem_id', 'gcp_id', 'created_at', 'updated_at'], 'integer'],
            ['enable_expertise', 'default', 'value' => EnableExpertise::OFF],
            ['enable_expertise', 'in', 'range' => [
                EnableExpertise::OFF,
                EnableExpertise::ON,
            ]],
            [['contractor_id', 'task_id'], 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Наименование ГMVP',
            'description' => 'Описание',
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


    public function init()
    {

        $this->on(self::EVENT_CLICK_BUTTON_CONFIRM, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_UPDATE, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_DELETE, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        parent::init();
    }


    /**
     * Разрешение эксертизы и отправка уведомлений
     * эксперту и трекеру (если на проект назначен экперт)
     *
     * @return bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function allowExpertise(): bool
    {
        if ($this->getEnableExpertise() === EnableExpertise::ON) {
            return true;
        }

        $user = $this->project->user;
        if ($expertIds = ProjectCommunications::getExpertIdsByProjectId($this->getProjectId())) {
            $transaction = Yii::$app->db->beginTransaction();

            $communicationIds = [];
            foreach ($expertIds as $i => $expertId) {
                $communication = new ProjectCommunications();
                $communication->setParams($expertId, $this->getProjectId(), CommunicationTypes::USER_ALLOWED_MVP_EXPERTISE, $this->getId());
                if ($i === 0 && $communication->save() && DuplicateCommunications::create($communication, $user->admin, TypesDuplicateCommunication::USER_ALLOWED_EXPERTISE)) {
                    $communicationIds[] = $communication->getId();
                    SendingCommunicationsToEmail::allowExpertiseToStageProject($communication, true);
                } elseif ($communication->save()) {
                    $communicationIds[] = $communication->getId();
                    SendingCommunicationsToEmail::allowExpertiseToStageProject($communication);
                }
            }

            if (count($communicationIds) === count($expertIds)) {
                $this->setEnableExpertise();
                if ($this->update()) {
                    $transaction->commit();
                    return true;
                }
            }

            $transaction->rollBack();
            return false;
        }

        $this->setEnableExpertise();
        return (bool)$this->update();
    }


    /**
     * Отправка писем трекеру и экспертам.
     * Чтобы не ломать код в случае ошибки при отправке письма,
     * выводим этот код в отдельный блок
     *
     * @param ProjectCommunications[] $communications
     * @return void
     */
    private function sendingCommunicationsToEmail(array $communications): void
    {
        try {
            if ($communications) {
                foreach ($communications as $k => $communication) {
                    SendingCommunicationsToEmail::softDeleteStageProject($communication, $k === 0);
                }
            }
        } catch (\Exception $exception) {}
    }


    /**
     * @param bool $sendCommunications
     * @return false|int
     * @throws Throwable
     */
    public function softDeleteStage(bool $sendCommunications = true)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $communications = [];
            if ($sendCommunications && ($this->getEnableExpertise() === EnableExpertise::ON) && $expertIds = ProjectCommunications::getExpertIdsByProjectId($this->getProjectId())) {
                $user = $this->project->user;
                foreach ($expertIds as $i => $expertId) {
                    $communication = new ProjectCommunications();
                    $communication->setParams($expertId, $this->getProjectId(), CommunicationTypes::USER_DELETED_MVP, $this->getId());
                    if ($i === 0 && $communication->save() && DuplicateCommunications::create($communication, $user->admin, TypesDuplicateCommunication::USER_DELETE_STAGE_PROJECT)) {
                        $communications[] = $communication;
                    } elseif ($communication->save()) {
                        $communications[] = $communication;
                    }
                }
            }

            $this->sendingCommunicationsToEmail($communications);

            if ($businessModel = $this->businessModel) {
                $businessModel->softDelete(['id' => $businessModel->getId()]);
            }

            if ($confirm = $this->confirm) {

                $responds = $confirm->responds;
                foreach ($responds as $respond) {

                    InterviewConfirmMvp::softDeleteAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmMvp::softDeleteAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmMvp::softDeleteAll(['confirm_id' => $confirm->getId()]);
                RespondsMvp::softDeleteAll(['confirm_id' => $confirm->getId()]);

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {
                    // Изменение статусов заданий исполнителей на "Удалено"
                    if (!ContractorTasks::deleteByParams(StageExpertise::CONFIRM_MVP, $confirm->getId())) {
                        $transaction->rollBack();
                        return false;
                    }
                }

                $confirm->softDelete(['id' => $confirm->getId()]);
            }

            // Удаление кэша для форм MVP
            $cachePathDelete = '../runtime/cache/forms/user-' . $this->project->user->getId() . '/projects/project-' . $this->project->getId() . '/segments/segment-' . $this->segment->getId() .
                '/problems/problem-' . $this->problem->getId() . '/gcps/gcp-' . $this->gcp->getId() . '/mvps/mvp-' . $this->getId();
            if (file_exists($cachePathDelete)) {
                FileHelper::removeDirectory($cachePathDelete);
            }

            $result = $this->softDelete(['id' => $this->getId()]);
            $transaction->commit();
            return $result;

        } catch (\Exception $exception) {
            $transaction->rollBack();
            return false;
        }
    }


    /**
     * @return false|int
     * @throws Throwable
     */
    public function recoveryStage()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var $businessModel BusinessModel */
            $businessModel = BusinessModel::find(false)
                ->andWhere(['mvp_id' => $this->getId()])
                ->one();

            if ($businessModel) {
                $businessModel->recovery(['id' => $businessModel->getId()]);
            }

            /** @var $confirm ConfirmMvp */
            $confirm = ConfirmMvp::find(false)
                ->andWhere(['mvp_id' => $this->getId()])
                ->one();

            if ($confirm) {
                /** @var $responds RespondsMvp[] */
                $responds = RespondsMvp::find(false)
                    ->andWhere(['confirm_id' => $confirm->getId()])
                    ->all();

                foreach ($responds as $respond) {

                    InterviewConfirmMvp::recoveryAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmMvp::recoveryAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmMvp::recoveryAll(['confirm_id' => $confirm->getId()]);
                RespondsMvp::recoveryAll(['confirm_id' => $confirm->getId()]);
                $confirm->recovery(['id' => $confirm->getId()]);

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {
                    // Воостановление статусов заданий исполнителей
                    if (!ContractorTasks::recoveryByParams(StageExpertise::CONFIRM_MVP, $confirm->getId())) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }

            $result = $this->recovery(['id' => $this->getId()]);
            $transaction->commit();
            return $result;

        } catch (\Exception $exception) {
            $transaction->rollBack();
            return false;
        }
    }


    /**
     * @return false|int
     * @throws Throwable
     */
    public function deleteStage()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($businessModel = $this->businessModel) {
                $businessModel->delete();
            }

            if ($confirm = $this->confirm) {

                $responds = $confirm->responds;
                foreach ($responds as $respond) {

                    InterviewConfirmMvp::deleteAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmMvp::deleteAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmMvp::deleteAll(['confirm_id' => $confirm->getId()]);
                RespondsMvp::deleteAll(['confirm_id' => $confirm->getId()]);
                $confirm->delete();
            }

            // Удаление директории MVP
            $gcpPathDelete = UPLOAD.'/user-'.$this->project->user->getId().'/project-'.$this->project->getId().'/segments/segment-'.$this->segment->getId().
                '/problems/problem-'.$this->problem->getId().'/gcps/gcp-'.$this->gcp->getId().'/mvps/mvp-'.$this->getId();
            if (file_exists($gcpPathDelete)) {
                FileHelper::removeDirectory($gcpPathDelete);
            }

            // Удаление кэша для форм MVP
            $cachePathDelete = '../runtime/cache/forms/user-'.$this->project->user->getId().'/projects/project-'.$this->project->getId().'/segments/segment-'.$this->segment->getId().
                '/problems/problem-'.$this->problem->getId().'/gcps/gcp-'.$this->gcp->getId().'/mvps/mvp-'.$this->getId();
            if (file_exists($cachePathDelete)) {
                FileHelper::removeDirectory($cachePathDelete);
            }

            // Удаление MVP
            $result = $this->delete();
            $transaction->commit();
            return $result;

        } catch (\Exception $exception) {
            $transaction->rollBack();
            return false;
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
    public function getBasicConfirmId(): int
    {
        return $this->basic_confirm_id;
    }

    /**
     * @param int $basic_confirm_id
     */
    public function setBasicConfirmId(int $basic_confirm_id): void
    {
        $this->basic_confirm_id = $basic_confirm_id;
    }

    /**
     * @param int $segment_id
     */
    public function setSegmentId(int $segment_id): void
    {
        $this->segment_id = $segment_id;
    }

    /**
     * @return int
     */
    public function getSegmentId(): int
    {
        return $this->segment_id;
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
    public function getProjectId(): int
    {
        return $this->project_id;
    }

    /**
     * @param int $problem_id
     */
    public function setProblemId(int $problem_id): void
    {
        $this->problem_id = $problem_id;
    }

    /**
     * @return int
     */
    public function getProblemId(): int
    {
        return $this->problem_id;
    }

    /**
     * @param int $gcp_id
     */
    public function setGcpId(int $gcp_id): void
    {
        $this->gcp_id = $gcp_id;
    }

    /**
     * @return int
     */
    public function getGcpId(): int
    {
        return $this->gcp_id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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

    /**
     * @return int|null
     */
    public function getTimeConfirm(): ?int
    {
        return $this->time_confirm;
    }

    /**
     * @param int|null $time_confirm
     */
    public function setTimeConfirm(int $time_confirm = null): void
    {
        $time_confirm ? $this->time_confirm = $time_confirm : $this->time_confirm = time();
    }

    /**
     * @return int|null
     */
    public function getExistConfirm(): ?int
    {
        return $this->exist_confirm;
    }

    /**
     * @param int $exist_confirm
     */
    public function setExistConfirm(int $exist_confirm): void
    {
        $this->exist_confirm = $exist_confirm;
    }

    /**
     * @return PropertyContainer
     */
    public function getPropertyContainer(): PropertyContainer
    {
        return $this->propertyContainer;
    }

    /**
     *
     */
    public function setPropertyContainer(): void
    {
        $this->propertyContainer = new PropertyContainer();
    }

    /**
     * @return string
     */
    public function getEnableExpertise(): string
    {
        return $this->enable_expertise;
    }

    /**
     *  Установить разрешение на экспертизу
     */
    public function setEnableExpertise(): void
    {
        $this->enable_expertise = EnableExpertise::ON;
        $this->setEnableExpertiseAt(time());
    }

    /**
     * @return int|null
     */
    public function getEnableExpertiseAt(): ?int
    {
        return $this->enable_expertise_at;
    }

    /**
     * @param int $enable_expertise_at
     */
    public function setEnableExpertiseAt(int $enable_expertise_at): void
    {
        $this->enable_expertise_at = $enable_expertise_at;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }

    /**
     * @param int $deleted_at
     */
    public function setDeletedAt(int $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @return int|null
     */
    public function getContractorId(): ?int
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
     * @return int|null
     */
    public function getTaskId(): ?int
    {
        return $this->task_id;
    }

    /**
     * @param int $task_id
     */
    public function setTaskId(int $task_id): void
    {
        $this->task_id = $task_id;
    }

    /**
     * @return string
     */
    public function getNameOfClass(): string
    {
        return static::class;
    }
}
