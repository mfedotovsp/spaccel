<?php

namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\FileHelper;

/**
 * Класс, который хранит объекты проблем сегментов в бд
 *
 * Class Problems
 * @package app\models
 *
 * @property int $id                                                        Идентификатор записи в таб. problems
 * @property int $basic_confirm_id                                          Идентификатор записи в таб. confirm_segment
 * @property int $segment_id                                                Идентификатор записи в таб. segments
 * @property int $project_id                                                Идентификатор записи в таб. projects
 * @property string $title                                                  Сформированное системой название проблемы
 * @property string $description                                            Описание проблемы
 * @property int $indicator_positive_passage                                Показатель прохождения теста
 * @property int $created_at                                                Дата создания проблемы
 * @property int $updated_at                                                Дата обновления проблемы
 * @property int $time_confirm                                              Дата подверждения проблемы
 * @property int $exist_confirm                                             Параметр факта подтверждения проблемы
 * @property string $enable_expertise                                       Параметр разрешения на экспертизу по даному этапу
 * @property int|null $enable_expertise_at                                  Дата разрешения на экспертизу по даному этапу
 * @property int|null $deleted_at                                           Дата удаления
 * @property int|null $contractor_id                                        Идентификатор исполнителя, создавшего проблему (если null - проблема создана проектантом)
 * @property int|null $task_id                                              Идентификатор задания исполнителя, по которому создана проблема (если null - проблема создана проектантом)
 * @property PropertyContainer $propertyContainer                           Свойство для реализации шаблона 'контейнер свойств'
 *
 * @property Gcps[] $gcps                                                   Ценностные предложения
 * @property Mvps[] $mvps                                                   Mvp-продукты
 * @property BusinessModel[] $businessModels                                Бизнес-модели
 * @property ConfirmProblem $confirm                                        Подтверждение проблемы
 * @property Segments $segment                                              Сегмент
 * @property Projects $project                                              Проект
 * @property RespondsSegment[] $respondsAgents                              Представители сегмента
 * @property ExpectedResultsInterviewConfirmProblem[] $expectedResults      Вопросы для проверки и ответы на них создаются на этапе генерации проблем сегмента
 * @property User|null $contractor                                          Исполнитель создавший проблему
 */
class Problems extends ActiveRecord
{
    use SoftDeleteModelTrait;

    public const EVENT_CLICK_BUTTON_CONFIRM = 'event click button confirm';

    public $propertyContainer;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'problems';
    }


    /**
     * Problems constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setPropertyContainer();
        parent::__construct($config);
    }


    /**
     * Получить все объекты Gcp данной проблемы
     *
     * @return ActiveQuery
     */
    public function getGcps(): ActiveQuery
    {
        return $this->hasMany(Gcps::class, ['problem_id' => 'id']);
    }


    /**
     * Получить все объекты Mvp данной проблемы
     *
     * @return ActiveQuery
     */
    public function getMvps(): ActiveQuery
    {
        return $this->hasMany(Mvps::class, ['problem_id' => 'id']);
    }


    /**
     * Получить все бизнес-модели данной проблемы
     *
     * @return ActiveQuery
     */
    public function getBusinessModels(): ActiveQuery
    {
        return $this->hasMany(BusinessModel::class, ['problem_id' => 'id']);
    }


    /**
     * Получить объект подтверждения данной проблемы
     *
     * @return ActiveQuery
     */
    public function getConfirm(): ActiveQuery
    {
        return $this->hasOne(ConfirmProblem::class, ['problem_id' => 'id']);
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
     * Получить объект текущего проекта
     *
     * @return ActiveQuery
     */
    public function getProject (): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * Исполнитель создавший проблему
     * @return User|null
     */
    public function getContractor(): ?User
    {
        return $this->getContractorId() ? User::findOne($this->getContractorId()) : null;
    }


    /**
     * Получить представителей сегмента, которых опросил руководитель проекта
     *
     * @return array|ActiveRecord[]
     */
    public function getRespondsAgents(): array
    {
        return RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $this->getBasicConfirmId(), 'interview_confirm_segment.status' => '1'])
            ->andWhere(['contractor_id' => null])
            ->all();
    }


    /**
     * Вопросы для проверки и ответы на них
     * создаются на этапе генерации проблем сегмента
     *
     * @return ActiveQuery
     */
    public function getExpectedResults(): ActiveQuery
    {
        return $this->hasMany(ExpectedResultsInterviewConfirmProblem::class, ['problem_id' => 'id']);
    }


    /**
     * Список вопросов для проверки и ответов на них
     *
     * @return string
     */
    public function getListExpectedResultsInterview(): string
    {
        $str = ''; $n = 1;
        foreach ($this->expectedResults as $expectedResult) {
            $str .= '<b>' . $n . '.</b> ' . $expectedResult->getQuestion() . ' (' . $expectedResult->getAnswer() . ') </br>';
            $n++;
        }
        return $str;
    }


    /**
     * @return array
     */
    public static function getValuesForSelectIndicatorPositivePassage(): array
    {
        return [
            5 => 'Показатель прохождения теста - 5%',
            10 => 'Показатель прохождения теста - 10%',
            15 => 'Показатель прохождения теста - 15%',
            20 => 'Показатель прохождения теста - 20%',
            25 => 'Показатель прохождения теста - 25%',
            30 => 'Показатель прохождения теста - 30%',
            35 => 'Показатель прохождения теста - 35%',
            40 => 'Показатель прохождения теста - 40%',
            45 => 'Показатель прохождения теста - 45%',
            50 => 'Показатель прохождения теста - 50%',
            55 => 'Показатель прохождения теста - 55%',
            60 => 'Показатель прохождения теста - 60%',
            65 => 'Показатель прохождения теста - 65%',
            70 => 'Показатель прохождения теста - 70%',
            75 => 'Показатель прохождения теста - 75%',
            80 => 'Показатель прохождения теста - 80%',
            85 => 'Показатель прохождения теста - 85%',
            90 => 'Показатель прохождения теста - 90%',
            95 => 'Показатель прохождения теста - 95%',
            100 => 'Показатель прохождения теста - 100%',
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['basic_confirm_id', 'title'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 2000],
            [['title', 'description'], 'trim'],
            [['indicator_positive_passage', 'time_confirm', 'basic_confirm_id', 'exist_confirm', 'segment_id', 'project_id', 'created_at', 'updated_at'], 'integer'],
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
            'title' => 'Название ГПС',
            'description' => 'Описание гипотезы проблемы сегмента',
            'indicator_positive_passage' => 'Показатель прохождения теста',
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
            ExpectedResultsInterviewConfirmProblem::deleteAll(['problem_id' => $this->getId()]);
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
                $communication->setParams($expertId, $this->getProjectId(), CommunicationTypes::USER_ALLOWED_PROBLEM_EXPERTISE, $this->getId());
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
                    $communication->setParams($expertId, $this->getProjectId(), CommunicationTypes::USER_DELETED_PROBLEM, $this->getId());
                    if ($i === 0 && $communication->save() && DuplicateCommunications::create($communication, $user->admin, TypesDuplicateCommunication::USER_DELETE_STAGE_PROJECT)) {
                        $communications[] = $communication;
                    } elseif ($communication->save()) {
                        $communications[] = $communication;
                    }
                }
            }

            $this->sendingCommunicationsToEmail($communications);

            if ($gcps = $this->gcps) {
                foreach ($gcps as $gcp) {
                    $gcp->softDeleteStage(false);
                }
            }

            if ($confirm = $this->confirm) {

                $responds = $confirm->responds;
                foreach ($responds as $respond) {

                    InterviewConfirmProblem::softDeleteAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmProblem::softDeleteAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmProblem::softDeleteAll(['confirm_id' => $confirm->getId()]);
                RespondsProblem::softDeleteAll(['confirm_id' => $confirm->getId()]);

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {
                    // Изменение статусов заданий исполнителей на "Удалено"
                    if (!ContractorTasks::deleteByParams(StageExpertise::CONFIRM_PROBLEM, $confirm->getId()) ||
                        !ContractorTasks::deleteByParams(StageExpertise::GCP, $confirm->getId())) {
                        $transaction->rollBack();
                        return false;
                    }
                }

                $confirm->softDelete(['id' => $confirm->getId()]);
            }

            ExpectedResultsInterviewConfirmProblem::softDeleteAll(['problem_id' => $this->getId()]);

            // Удаление кэша для форм проблемы
            $cachePathDelete = '../runtime/cache/forms/user-'.$this->project->user->getId().'/projects/project-'.$this->project->getId().'/segments/segment-'.$this->segment->getId().'/problems/problem-'.$this->getId();
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
            /** @var $gcps Gcps[] */
            $gcps = Gcps::find(false)
                ->andWhere(['problem_id' => $this->getId()])
                ->all();

            if (count($gcps) > 0) {
                foreach ($gcps as $gcp) {
                    $gcp->recoveryStage();
                }
            }

            /** @var $confirm ConfirmProblem */
            $confirm = ConfirmProblem::find(false)
                ->andWhere(['problem_id' => $this->getId()])
                ->one();

            if ($confirm) {

                /** @var $responds RespondsProblem[] */
                $responds = RespondsProblem::find(false)
                    ->andWhere(['confirm_id' => $confirm->getId()])
                    ->all();

                foreach ($responds as $respond) {

                    InterviewConfirmProblem::recoveryAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmProblem::recoveryAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmProblem::recoveryAll(['confirm_id' => $confirm->getId()]);
                RespondsProblem::recoveryAll(['confirm_id' => $confirm->getId()]);
                $confirm->recovery(['id' => $confirm->getId()]);

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {
                    // Воостановление статусов заданий исполнителей
                    if (!ContractorTasks::recoveryByParams(StageExpertise::CONFIRM_PROBLEM, $confirm->getId()) ||
                        !ContractorTasks::recoveryByParams(StageExpertise::GCP, $confirm->getId())) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }

            ExpectedResultsInterviewConfirmProblem::recoveryAll(['problem_id' => $this->getId()]);

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
            if ($gcps = $this->gcps) {
                foreach ($gcps as $gcp) {
                    $gcp->deleteStage();
                }
            }

            if ($confirm = $this->confirm) {

                $responds = $confirm->responds;
                foreach ($responds as $respond) {

                    InterviewConfirmProblem::deleteAll(['respond_id' => $respond->getId()]);
                    AnswersQuestionsConfirmProblem::deleteAll(['respond_id' => $respond->getId()]);
                }

                QuestionsConfirmProblem::deleteAll(['confirm_id' => $confirm->getId()]);
                RespondsProblem::deleteAll(['confirm_id' => $confirm->getId()]);

                $confirm->delete();
            }

            ExpectedResultsInterviewConfirmProblem::deleteAll(['problem_id' => $this->getId()]);

            // Удаление директории проблемы
            $problemPathDelete = UPLOAD.'/user-'.$this->project->user->getId().'/project-'.$this->project->getId().'/segments/segment-'.$this->segment->getId().'/problems/problem-'.$this->getId();
            if (file_exists($problemPathDelete)) {
                FileHelper::removeDirectory($problemPathDelete);
            }

            // Удаление кэша для форм проблемы
            $cachePathDelete = '../runtime/cache/forms/user-'.$this->project->user->getId().'/projects/project-'.$this->project->getId().'/segments/segment-'.$this->segment->getId().'/problems/problem-'.$this->getId();
            if (file_exists($cachePathDelete)) {
                FileHelper::removeDirectory($cachePathDelete);
            }

            // Удаление проблемы
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
     * @param int $id
     */
    public function setSegmentId(int $id): void
    {
        $this->segment_id = $id;
    }

    /**
     * @return int
     */
    public function getSegmentId(): int
    {
        return $this->segment_id;
    }

    /**
     * @param int $id
     */
    public function setProjectId(int $id): void
    {
        $this->project_id = $id;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->project_id;
    }

    /**
     * @return int
     */
    public function getConfirmSegmentId(): int
    {
        return $this->basic_confirm_id;
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
    public function getIndicatorPositivePassage(): int
    {
        return $this->indicator_positive_passage;
    }

    /**
     * @param int $indicator_positive_passage
     */
    public function setIndicatorPositivePassage(int $indicator_positive_passage): void
    {
        $this->indicator_positive_passage = $indicator_positive_passage;
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
     * Параметр разрешения экспертизы
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
