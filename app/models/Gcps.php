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
 * Класс, который хранит объекты ценностных предложений в бд
 *
 * Class Gcps
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. gcps
 * @property int $basic_confirm_id                  Идентификатор записи в таб. confirm_problem
 * @property int $segment_id                        Идентификатор записи в таб. segments
 * @property int $project_id                        Идентификатор записи в таб. projects
 * @property int $problem_id                        Идентификатор записи в таб. problems
 * @property string $title                          Сформированное системой название ценностного предложения
 * @property string $description                    Описание ценностного предложения
 * @property int $created_at                        Дата создания ЦП
 * @property int $updated_at                        Дата обновления ЦП
 * @property int $time_confirm                      Дата подверждения ЦП
 * @property int $exist_confirm                     Параметр факта подтверждения ЦП
 * @property string $enable_expertise               Параметр разрешения на экспертизу по даному этапу
 * @property int|null $enable_expertise_at          Дата разрешения на экспертизу по даному этапу
 * @property int|null $deleted_at                   Дата удаления
 * @property int|null $contractor_id                Идентификатор исполнителя, создавшего ЦП (если null - ЦП создано проектантом)
 * @property int|null $task_id                      Идентификатор задания исполнителя, по которому создано ЦП (если null - ЦП создано проектантом)
 * @property PropertyContainer $propertyContainer   Свойство для реализации шаблона 'контейнер свойств'
 *
 * @property ConfirmGcp $confirm                    Подтверждение ценностного предложения
 * @property BusinessModel[] $businessModels        Бизнес-модели
 * @property Mvps[] $mvps                           Mvp-продукты
 * @property Problems $problem                      Проблема
 * @property Segments $segment                      Сегмент
 * @property Projects $project                      Проект
 * @property RespondsProblem[] $respondsAgents      Респонденты, которые подтвердили текущую проблему
 * @property User|null $contractor                  Исполнитель создавший ГЦП
 */
class Gcps extends ActiveRecord
{
    use SoftDeleteModelTrait;

    public const EVENT_CLICK_BUTTON_CONFIRM = 'event click button confirm';

    public $propertyContainer;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'gcps';
    }


    /**
     * Gcps constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setPropertyContainer();
        parent::__construct($config);
    }


    /**
     * Получить объект подтверждения данного Gcps
     *
     * @return ActiveQuery
     */
    public function getConfirm(): ActiveQuery
    {
        return $this->hasOne(ConfirmGcp::class, ['gcp_id' => 'id']);
    }


    /**
     * @return int
     */
    public function getConfirmProblemId(): int
    {
        return $this->basic_confirm_id;
    }


    /**
     * Получить все бизнес-модели данного Gcps
     *
     * @return ActiveQuery
     */
    public function getBusinessModels(): ActiveQuery
    {
        return $this->hasMany(BusinessModel::class, ['gcp_id' => 'id']);
    }


    /**
     * Получить все объекты Mvps данного Gcps
     *
     * @return ActiveQuery
     */
    public function getMvps(): ActiveQuery
    {
        return $this->hasMany(Mvps::class, ['gcp_id' => 'id']);
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
     * Получить объект текущего сегмента
     *
     * @return ActiveQuery
     */
    public function getSegment (): ActiveQuery
    {
        return $this->hasOne(Segments::class, ['id' => 'segment_id']);
    }


    /**
     * Получить объект текущего проекта
     *
     * @return ActiveQuery
     */
    public function getProject (): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * Получить респондентов, которые
     * подтвердтлт текущую проблему
     *
     * @return array|ActiveRecord[]
     */
    public function getRespondsAgents(): array
    {
        return RespondsProblem::find()->with('interview')
            ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
            ->andWhere(['confirm_id' => $this->getConfirmProblemId(), 'interview_confirm_problem.status' => '1'])
            ->andWhere(['contractor_id' => null])
            ->all();
    }


    /**
     * Исполнитель создавший ГЦП
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
            [['title', 'description'], 'trim'],
            [['time_confirm', 'basic_confirm_id', 'exist_confirm', 'project_id', 'segment_id', 'problem_id', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string', 'max' => 1500],
            [['title'], 'string', 'max' => 255],
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
            'title' => 'Наименование ГЦП',
            'description' => 'Формулировка ГЦП',
            'date_create' => 'Дата создания',
            'date_confirm' => 'Дата подтверждения'
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
                $communication->setParams($expertId, $this->getProjectId(), CommunicationTypes::USER_ALLOWED_GCP_EXPERTISE, $this->getId());
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
                    $communication->setParams($expertId, $this->getProjectId(), CommunicationTypes::USER_DELETED_GCP, $this->getId());
                    if ($i === 0 && $communication->save() && DuplicateCommunications::create($communication, $user->admin, TypesDuplicateCommunication::USER_DELETE_STAGE_PROJECT)) {
                        $communications[] = $communication;
                    } elseif ($communication->save()) {
                        $communications[] = $communication;
                    }
                }
            }

            $this->sendingCommunicationsToEmail($communications);

            if ($mvps = $this->mvps) {
                foreach ($mvps as $mvp) {
                    $mvp->softDeleteStage(false);
                }
            }

            if ($confirm = $this->confirm) {

                $responds = $confirm->responds;
                foreach ($responds as $respond) {

                    InterviewConfirmGcp::softDeleteAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmGcp::softDeleteAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmGcp::softDeleteAll(['confirm_id' => $confirm->getId()]);
                RespondsGcp::softDeleteAll(['confirm_id' => $confirm->getId()]);

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {
                    // Изменение статусов заданий исполнителей на "Удалено"
                    if (!ContractorTasks::deleteByParams(StageExpertise::CONFIRM_GCP, $confirm->getId()) ||
                        !ContractorTasks::deleteByParams(StageExpertise::MVP, $confirm->getId())) {
                        $transaction->rollBack();
                        return false;
                    }
                }

                $confirm->softDelete(['id' => $confirm->getId()]);
            }

            // Удаление кэша для форм ГЦП
            $cachePathDelete = '../runtime/cache/forms/user-' . $this->project->user->getId() . '/projects/project-' . $this->project->getId() . '/segments/segment-' . $this->segment->getId() .
                '/problems/problem-' . $this->problem->getId() . '/gcps/gcp-' . $this->getId();
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
            /** @var $mvps Mvps[] */
            $mvps = Mvps::find(false)
                ->andWhere(['gcp_id' => $this->getId()])
                ->all();

            if (count($mvps) > 0) {
                foreach ($mvps as $mvp) {
                    $mvp->recoveryStage();
                }
            }

            /** @var $confirm ConfirmGcp */
            $confirm = ConfirmGcp::find(false)
                ->andWhere(['gcp_id' => $this->getId()])
                ->one();

            if ($confirm) {
                /** @var $responds RespondsGcp[] */
                $responds = RespondsGcp::find(false)
                    ->andWhere(['confirm_id' => $confirm->getId()])
                    ->all();

                foreach ($responds as $respond) {

                    InterviewConfirmGcp::recoveryAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmGcp::recoveryAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmGcp::recoveryAll(['confirm_id' => $confirm->getId()]);
                RespondsGcp::recoveryAll(['confirm_id' => $confirm->getId()]);
                $confirm->recovery(['id' => $confirm->getId()]);

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {
                    // Воостановление статусов заданий исполнителей
                    if (!ContractorTasks::recoveryByParams(StageExpertise::CONFIRM_GCP, $confirm->getId()) ||
                        !ContractorTasks::recoveryByParams(StageExpertise::MVP, $confirm->getId())) {
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
            if ($mvps = $this->mvps) {
                foreach ($mvps as $mvp) {
                    $mvp->deleteStage();
                }
            }

            if ($confirm = $this->confirm) {

                $responds = $confirm->responds;
                foreach ($responds as $respond) {

                    InterviewConfirmGcp::deleteAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmGcp::deleteAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmGcp::deleteAll(['confirm_id' => $confirm->getId()]);
                RespondsGcp::deleteAll(['confirm_id' => $confirm->getId()]);
                $confirm->delete();
            }

            // Удаление директории ГЦП
            $gcpPathDelete = UPLOAD . '/user-' . $this->project->user->getId() . '/project-' . $this->project->getId() . '/segments/segment-' . $this->segment->getId() .
                '/problems/problem-' . $this->problem->getId() . '/gcps/gcp-' . $this->getId();
            if (file_exists($gcpPathDelete)) {
                FileHelper::removeDirectory($gcpPathDelete);
            }

            // Удаление кэша для форм ГЦП
            $cachePathDelete = '../runtime/cache/forms/user-' . $this->project->user->getId() . '/projects/project-' . $this->project->getId() . '/segments/segment-' . $this->segment->getId() .
                '/problems/problem-' . $this->problem->getId() . '/gcps/gcp-' . $this->getId();
            if (file_exists($cachePathDelete)) {
                FileHelper::removeDirectory($cachePathDelete);
            }

            // Удаление ГЦП
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
