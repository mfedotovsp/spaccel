<?php

namespace app\models;

use app\models\interfaces\ConfirmationInterface;
use app\models\traits\SoftDeleteModelTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Класс, который хранит подтверждения проблем сегментов в бд
 *
 * Class ConfirmProblem
 * @package app\models
 *
 * @property int $id                                    Идентификатор записи в таб. confirm_problem
 * @property int $problem_id                            Идентификатор записи в таб. problems
 * @property int $count_respond                         Количество респондентов
 * @property int $count_positive                        Количество респондентов, подтверждающих проблему
 * @property string $need_consumer                      Потребность потребителя
 * @property string $enable_expertise                   Параметр разрешения на экспертизу по даному этапу
 * @property int|null $enable_expertise_at              Дата разрешения на экспертизу по даному этапу
 * @property int|null $deleted_at                       Дата удаления
 * @property boolean $exist_desc                        Флаг наличия описания подтверждения (учебный вариант)
 *
 * @property Problems $problem                          Проблема
 * @property RespondsProblem[] $responds                Респонденты, привязанные к подтверждению
 * @property Gcps[] $gcps                               Ценностные предложения
 * @property QuestionsConfirmProblem[] $questions       Вопросы, привязанные к подтверждению
 * @property Problems $hypothesis                       Гипотеза, к которой относится подтверждение
 *
 * @property ConfirmDescription|null $confirmDescription                   Описание подтверждения для учебного варианта
 */
class ConfirmProblem extends ActiveRecord implements ConfirmationInterface
{
    use SoftDeleteModelTrait;

    public const STAGE = 4;
    public const LIMIT_COUNT_RESPOND = 100;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'confirm_problem';
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
     * Получить объект текущей проблемы
     *
     * @return ActiveQuery
     */
    public function getProblem(): ActiveQuery
    {
        return $this->hasOne(Problems::class, ['id' => 'problem_id']);
    }


    /**
     * Получить респондентов привязанных к подтверждению
     *
     * @return ActiveQuery
     */
    public function getResponds(): ActiveQuery
    {
        return $this->hasMany(RespondsProblem::class, ['confirm_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getGcps(): ActiveQuery
    {
        return $this->hasMany(Gcps::class, ['basic_confirm_id' => 'id']);
    }


    /**
     * Получить вопросы привязанные к подтверждению
     *
     * @return ActiveQuery
     */
    public function getQuestions(): ActiveQuery
    {
        return $this->hasMany(QuestionsConfirmProblem::class, ['confirm_id' => 'id']);
    }


    /**
     * Получить гипотезу подтверждения
     *
     * @return ActiveQuery
     */
    public function getHypothesis(): ActiveQuery
    {
        return $this->hasOne(Problems::class, ['id' => 'problem_id']);
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
            ->andWhere(['type' => StageExpertise::CONFIRM_PROBLEM])
            ->one() ?: null;
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['problem_id', 'count_respond', 'count_positive'], 'required'],
            ['need_consumer', 'needConsumerValid'],
            [['problem_id'], 'integer'],
            ['need_consumer', 'trim'],
            ['need_consumer', 'string', 'max' => 255],
            ['count_respond', 'integer', 'integerOnly' => TRUE, 'min' => 0],
            ['count_positive', 'integer', 'integerOnly' => TRUE, 'min' => 1],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'max' => 100],
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
            'count_positive' => 'Необходимое количество позитивных ответов',
            'need_consumer' => 'Потребность потребителя',
        ];
    }


    public function init()
    {

        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->problem->project->touch('updated_at');
            $this->problem->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_UPDATE, function (){
            $this->problem->project->touch('updated_at');
            $this->problem->project->user->touch('updated_at');
        });

        parent::init();
    }


    public function needConsumerValid()
    {
        if (!$this->exist_desc && empty(trim($this->need_consumer))) {
            $this->addError(
                'problemVariants',
                'Заполните описание всех выявленных проблем'
            );
        }
    }


    /**
     * Список вопросов, который будет показан для добавления нового вопроса
     *
     * @return array
     */
    public function queryQuestionsGeneralList(): array
    {
        $user = $this->problem->project->user;
        $questions = array(); //Добавляем в массив вопросы уже привязанные к данной программе
        foreach ($this->questions as $question) {
            $questions[] = $question['title'];
        }

        /**
         * @var AllQuestionsConfirmProblem[] $attachQuestions
         */
        // Вопросы, предлагаемые по-умолчанию на данном этапе
        $defaultQuestions = AllQuestionsConfirmProblem::defaultListQuestions();
        // Вопросы, которые когда-либо добавлял пользователь на данном этапе
        $attachQuestions = AllQuestionsConfirmProblem::find()
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

        $count_interview = (int)RespondsProblem::find()->with('interview')
            ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
            ->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['interview_confirm_problem.id' => null]])->count();

        $count_positive = (int)RespondsProblem::find()->with('interview')
            ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
            ->andWhere(['confirm_id' => $this->getId(), 'interview_confirm_problem.status' => '1'])->count();

        if ($this->gcps || (count($this->responds) === $count_interview && $this->getCountPositive() <= $count_positive)) {
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
        return RespondsProblem::find()->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['info_respond' => '']])
            ->andWhere(['not', ['date_plan' => null]])->andWhere(['not', ['place_interview' => '']])->count();
    }


    /**
     * @return int|string
     */
    public function getCountDescInterviewsOfModel()
    {
        // Кол-во респондентов, у кот-х существует анкета
        return RespondsProblem::find()->with('interview')
            ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
            ->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['interview_confirm_problem.id' => null]])->count();
    }


    /**
     * @return int|string
     */
    public function getCountConfirmMembers()
    {
        //Кол-во респондентов, кот-е подтвердили проблему
        return RespondsProblem::find()->with('interview')
            ->leftJoin('interview_confirm_problem', '`interview_confirm_problem`.`respond_id` = `responds_problem`.`id`')
            ->andWhere(['confirm_id' => $this->getId(), 'interview_confirm_problem.status' => '1'])->count();
    }


    /**
     * Путь к папке всего
     * кэша данного подтверждения
     *
     * @return string
     */
    public function getCachePath(): string
    {
        $problem = $this->problem;
        $segment = $problem->segment;
        $project = $problem->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId().
            '/segments/segment-'.$segment->getId(). '/problems/problem-'.$problem->getId().'/confirm';
    }


    /**
     * Разрешение эксертизы и отправка уведомлений
     * эксперту и трекеру (если на проект назначен экперт)
     *
     * @param Problems $problem
     * @return bool
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function allowExpertise(Problems $problem): bool
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
                $communication->setParams($expertId, $project->getId(), CommunicationTypes::USER_ALLOWED_CONFIRM_PROBLEM_EXPERTISE, $this->getId());
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
                if ($this->update() && $problem->update()) {
                    $transaction->commit();
                    return true;
                }
            }

            $transaction->rollBack();
            return false;
        }

        $this->setEnableExpertise();
        if ($this->update() && $problem->update()) {
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
     * @return int
     */
    public function getProblemId(): int
    {
        return $this->problem_id;
    }

    /**
     * @param int $id
     */
    public function setProblemId(int $id): void
    {
        $this->problem_id = $id;
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
    public function getNeedConsumer(): string
    {
        return $this->need_consumer;
    }

    /**
     * @param string $needConsumer
     */
    public function setNeedConsumer(string $needConsumer): void
    {
        $this->need_consumer = $needConsumer;
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
