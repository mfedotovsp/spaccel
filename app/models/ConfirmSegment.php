<?php

namespace app\models;

use app\models\interfaces\ConfirmationInterface;
use app\models\traits\SoftDeleteModelTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Класс, который хранит объекты подтверждения сегментов в бд
 *
 * Class ConfirmSegment
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. confirm_segment
 * @property int $segment_id                        Идентификатор записи в таб. segments
 * @property int $count_respond                     Количество респондентов
 * @property int $count_positive                    Количество респондентов, соответствующих сегменту
 * @property string $greeting_interview             Приветствие в начале встречи
 * @property string $view_interview                 Информация о вас для респондентов
 * @property string $reason_interview               Причина и тема (что побудило) для проведения исследования
 * @property string $enable_expertise               Параметр разрешения на экспертизу по даному этапу
 * @property int|null $enable_expertise_at          Дата разрешения на экспертизу по даному этапу
 * @property int|null $deleted_at                   Дата удаления
 * @property boolean $exist_desc                    Флаг наличия описания подтверждения (учебный вариант)
 *
 * @property Segments $segment                      Сегмент
 * @property QuestionsConfirmSegment[] $questions   Вопросы, привязанные к подтверждению
 * @property RespondsSegment[] $responds            Респонденты, привязанные к подтверждению
 * @property Problems[] $problems                   Проблемы
 * @property Segments $hypothesis                   Гипотеза, к которой относится подтверждение
 *
 * @property ConfirmDescription|null $confirmDescription                    Описание подтверждения для учебного варианта
 * @property ProblemVariant[] $problemVariants                              Возможные проблемы в описании подтверждения для учебного варианта
 */
class ConfirmSegment extends ActiveRecord implements ConfirmationInterface
{
    use SoftDeleteModelTrait;

    public const STAGE = 2;
    public const LIMIT_COUNT_RESPOND = 100;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'confirm_segment';
    }


    /**
     * @return int
     */
    public function getStage(): int
    {
        return self::STAGE;
    }


    /**
     * Проверка на ограничение кол-ва респондентов
     *
     * @return bool
     */
    public function checkingLimitCountRespond(): bool
    {
        if ($this->getCountRespond() < self::LIMIT_COUNT_RESPOND) {
            return true;
        }
        return false;
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
     * Получить вопросы привязанные к подтверждению
     *
     * @return ActiveQuery
     */
    public function getQuestions(): ActiveQuery
    {
        return $this->hasMany(QuestionsConfirmSegment::class, ['confirm_id' => 'id']);
    }


    /**
     * Получить респондентов привязанных к подтверждению
     *
     * @return ActiveQuery
     */
    public function getResponds(): ActiveQuery
    {
        return $this->hasMany(RespondsSegment::class, ['confirm_id' => 'id']);
    }


    /**
     * Получить все проблемы по данному сегменту
     *
     * @return ActiveQuery
     */
    public function getProblems(): ActiveQuery
    {
        return $this->hasMany(Problems::class, ['basic_confirm_id' => 'id']);
    }


    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->setGreetingInterview($params['greeting_interview']);
        $this->setViewInterview($params['view_interview']);
        $this->setReasonInterview($params['reason_interview']);
    }


    /**
     * Получить гипотезу подтверждения
     * @return ActiveQuery
     */
    public function getHypothesis(): ActiveQuery
    {
        return $this->hasOne(Segments::class, ['id' => 'segment_id']);
    }

    /**
     * Получить описание подтверждения
     * для учебного варианта
     *
     * @return ActiveRecord|null
     */
    public function getConfirmDescription(): ?ActiveRecord
    {
       return ConfirmDescription::find()
            ->andWhere(['confirm_id' => $this->getId()])
            ->andWhere(['type' => StageExpertise::CONFIRM_SEGMENT])
            ->one() ?: null;
    }


    /**
     * Получить возможные проблемы в описании
     * подтверждения для учебного варианта
     *
     * @return ActiveQuery
     */
    public function getProblemVariants(): ActiveQuery
    {
        return $this->hasMany(ProblemVariant::class, ['confirm_id' => 'id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['segment_id', 'count_respond', 'count_positive', 'greeting_interview', 'view_interview', 'reason_interview'], 'required'],
            [['segment_id'], 'integer'],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'min' => '1'],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'max' => '100'],
            [['greeting_interview', 'view_interview', 'reason_interview'], 'string', 'max' => '2000'],
            [['greeting_interview', 'view_interview', 'reason_interview'], 'trim'],
            ['enable_expertise', 'default', 'value' => EnableExpertise::OFF],
            ['enable_expertise', 'in', 'range' => [
                EnableExpertise::OFF,
                EnableExpertise::ON,
            ]],
            ['exist_desc', 'default', 'value' => false],
            ['enable_expertise', 'boolean'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'count_respond' => 'Количество респондентов',
            'count_positive' => 'Количество респондентов, соответствующих сегменту',
            'greeting_interview' => 'Приветствие в начале встречи',
            'view_interview' => 'Информация о вас для респондентов',
            'reason_interview' => 'Причина и тема (что побудило) для проведения исследования',
        ];
    }


    public function init()
    {

        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->segment->project->touch('updated_at');
            $this->segment->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_UPDATE, function (){
            $this->segment->project->touch('updated_at');
            $this->segment->project->user->touch('updated_at');
        });

        parent::init();
    }


    /**
     * Список вопросов, который будет показан для добавления нового вопроса
     *
     * @return array
     */
    public function queryQuestionsGeneralList(): array
    {
        $user = $this->segment->project->user;
        $questions = array(); // Добавляем в массив вопросы уже привязанные к данной программе
        foreach ($this->questions as $question) {
            $questions[] = $question->getTitle();
        }

        /**
         * @var AllQuestionsConfirmSegment[] $attachQuestions
         */
        // Вопросы, предлагаемые по-умолчанию на данном этапе
        $defaultQuestions = AllQuestionsConfirmSegment::defaultListQuestions();
        // Вопросы, которые когда-либо добавлял пользователь на данном этапе
        $attachQuestions = AllQuestionsConfirmSegment::find()
            ->andWhere(['user_id' => $user->getId()])
            ->orderBy(['id' => SORT_DESC])
            ->select('title')
            ->asArray()
            ->all();


        $qs = array(); // Добавляем в массив вопросы, предлагаемые по-умолчанию на данном этапе
        foreach ($defaultQuestions as $question) {
            $qs[] = $question['title'];
        }
        // Убираем из списка вопросов, которые когда-либо добавлял пользователь на данном этапе
        // вопросы, которые совпадают  с вопросами по-умолчанию
        foreach ($attachQuestions as $key => $queryQuestion) {
            if (in_array($queryQuestion['title'], $qs, false)) {
                unset($attachQuestions[$key]);
            }
        }

        //Убираем из списка для добавления вопросов, вопросы уже привязанные к данной программе
        $queryQuestions = array_merge($defaultQuestions, $attachQuestions);
        foreach ($queryQuestions as $key => $queryQuestion) {
            if (in_array($queryQuestion['title'], $questions, false)) {
                unset($queryQuestions[$key]);
            }
        }

        return $queryQuestions;
    }


    /**
     * @return bool
     */
    public function getButtonMovingNextStage(): bool
    {

        $count_interview = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['interview_confirm_segment.id' => null]])->count();

        $count_positive = (int)RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $this->getId(), 'interview_confirm_segment.status' => '1'])->count();

        if ($this->problems || (count($this->responds) === $count_interview && $this->getCountPositive() <= $count_positive)) {
            return true;
        }

        return false;
    }


    /**
     * @return int|string
     */
    public function getCountRespondsOfModel()
    {
        //Кол-во респондентов, у кот-х заполнены данные
        return RespondsSegment::find()
            ->andWhere(['confirm_id' => $this->getId()])
            ->andWhere(['not', ['info_respond' => '']])
            ->andWhere(['not', ['date_plan' => null]])
            ->andWhere(['not', ['place_interview' => '']])
            ->count();
    }


    /**
     * @return int|string
     */
    public function getCountDescInterviewsOfModel()
    {
        // Кол-во респондентов, у кот-х существует интервью
        return RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['interview_confirm_segment.id' => null]])
            ->count();
    }


    /**
     * @return int|string
     */
    public function getCountConfirmMembers()
    {
        // Кол-во представителей сегмента
        return RespondsSegment::find()->with('interview')
            ->leftJoin('interview_confirm_segment', '`interview_confirm_segment`.`respond_id` = `responds_segment`.`id`')
            ->andWhere(['confirm_id' => $this->getId(), 'interview_confirm_segment.status' => '1'])
            ->count();
    }


    /**
     * Путь к папке всего
     * кэша данного подтверждения
     * @return string
     */
    public function getCachePath(): string
    {
        $segment = $this->segment;
        $project = $segment->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId().'/segments/segment-'.$segment->getId().'/confirm';
    }


    /**
     * Разрешение эксертизы и отправка уведомлений
     * эксперту и трекеру (если на проект назначен экперт)
     *
     * @param Segments $segment
     * @return bool
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function allowExpertise(Segments $segment): bool
    {
        if ($this->getEnableExpertise() === EnableExpertise::ON) {
            return true;
        }

        $project = $this->hypothesis->project;
        $user = $project->user;
        $transaction = Yii::$app->db->beginTransaction();
        if ($expertIds = ProjectCommunications::getExpertIdsByProjectId($project->getId())) {

            $communicationIds = [];
            foreach ($expertIds as $i => $expertId) {
                $communication = new ProjectCommunications();
                $communication->setParams($expertId, $project->getId(), CommunicationTypes::USER_ALLOWED_CONFIRM_SEGMENT_EXPERTISE, $this->getId());
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
                if ($this->update() && $segment->update()) {
                    $transaction->commit();
                    return true;
                }
            }

            $transaction->rollBack();
            return false;
        }

        $this->setEnableExpertise();
        if ($this->update() && $segment->update()) {
            $transaction->commit();
            return true;
        }

        $transaction->rollBack();
        return false;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return int
     */
    public function getCountRespond(): int
    {
        return $this->count_respond;
    }

    /**
     * @param int $count
     */
    public function setCountRespond(int $count): void
    {
        $this->count_respond = $count;
    }

    /**
     * @return int
     */
    public function getCountPositive(): int
    {
        return $this->count_positive;
    }

    /**
     * @param int $count
     */
    public function setCountPositive(int $count): void
    {
        $this->count_positive = $count;
    }

    /**
     * @return string
     */
    public function getGreetingInterview(): string
    {
        return $this->greeting_interview;
    }

    /**
     * @param string $greeting_interview
     */
    public function setGreetingInterview(string $greeting_interview): void
    {
        $this->greeting_interview = $greeting_interview;
    }

    /**
     * @return string
     */
    public function getViewInterview(): string
    {
        return $this->view_interview;
    }

    /**
     * @param string $view_interview
     */
    public function setViewInterview(string $view_interview): void
    {
        $this->view_interview = $view_interview;
    }

    /**
     * @return string
     */
    public function getReasonInterview(): string
    {
        return $this->reason_interview;
    }

    /**
     * @param string $reason_interview
     */
    public function setReasonInterview(string $reason_interview): void
    {
        $this->reason_interview = $reason_interview;
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
     * @return bool
     */
    public function isExistDesc(): bool
    {
        return $this->exist_desc;
    }

    /**
     * @param bool $exist_desc
     */
    public function setExistDesc(bool $exist_desc): void
    {
        $this->exist_desc = $exist_desc;
    }

}
