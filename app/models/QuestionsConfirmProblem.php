<?php


namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Класс хранит в бд вопросы интервью на этапе подтверждения гипотезы проблемы
 *
 * Class QuestionsConfirmProblem
 * @package app\models
 *
 * @property int $id                                                                Идентификатор записи в таб. questions_confirm_problem
 * @property int $confirm_id                                                        Идентификатор записи в таб. confirm_problem
 * @property string $title                                                          Описание вопроса
 * @property int $status                                                            Параметр указывает на важность вопроса
 * @property int $created_at                                                        Дата создания вопроса
 * @property int $updated_at                                                        Дата обновления вопроса
 * @property int|null $deleted_at                                                   Дата удаления
 * @property ManagerForAnswersAtQuestion $_manager_answers                          Менеджер по ответам на вопросы
 * @property CreatorQuestionToGeneralList $_creator_question_to_general_list        Менеджер, который добавляет вопросы в таблицы, которые содержат все вопросы добавляемые на этапах подтверждения гипотез
 *
 * @property ConfirmProblem $confirm                                                Подтверждение проблемы
 * @property AnswersQuestionsConfirmProblem[] $answers                              Все ответы на данный вопрос
 */
class QuestionsConfirmProblem extends ActiveRecord
{
    use SoftDeleteModelTrait;

    private $_manager_answers;
    private $_creator_question_to_general_list;


    /**
     * QuestionsConfirmProblem constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setManagerAnswers();
        $this->setCreatorQuestionToGeneralList();
        parent::__construct($config);
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'questions_confirm_problem';
    }


    /**
     * Получить объект подтверждения
     *
     * @return ActiveQuery
     */
    public function getConfirm (): ActiveQuery
    {
        return $this->hasOne(ConfirmProblem::class, ['id' => 'confirm_id']);
    }


    /**
     * Получить все ответы на данный вопрос
     *
     * @return array|ActiveRecord[]
     */
    public function getAnswers(): array
    {
        return AnswersQuestionsConfirmProblem::find()->andWhere(['question_id' => $this->getId()])
            ->andWhere(['not', ['answers_questions_confirm_problem.answer' => '']])->all();
    }


    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->setConfirmId($params['confirm_id']);
        $this->setTitle($params['title']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['confirm_id', 'title'], 'required'],
            [['confirm_id', 'created_at', 'updated_at'], 'integer'],
            ['status', 'default', 'value' => QuestionStatus::STATUS_NOT_STAR],
            ['status', 'in', 'range' => [
                QuestionStatus::STATUS_NOT_STAR,
                QuestionStatus::STATUS_ONE_STAR
            ]],
            [['title'], 'string', 'max' => 255],
            [['title'], 'trim'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return ['title' => 'Описание вопроса'];
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

        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->confirm->problem->project->touch('updated_at');
            $this->confirm->problem->project->user->touch('updated_at');
            $this->getManagerAnswers()->create($this->confirm, $this->getId());
            $this->getCreatorQuestionToGeneralList()->create($this->confirm, $this->getTitle());
        });

        $this->on(self::EVENT_AFTER_UPDATE, function (){
            $this->confirm->problem->project->touch('updated_at');
            $this->confirm->problem->project->user->touch('updated_at');
            $this->getCreatorQuestionToGeneralList()->create($this->confirm, $this->getTitle());
        });

        $this->on(self::EVENT_AFTER_DELETE, function (){
            $this->confirm->problem->project->touch('updated_at');
            $this->confirm->problem->project->user->touch('updated_at');
            $this->getManagerAnswers()->delete($this->confirm, $this->getId());
        });

        parent::init();
    }


    /**
     * @return array|bool
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function deleteAndGetData()
    {
        // Получить список вопросов без удаленного вопроса
        $questions = self::find()->andWhere(['confirm_id' => $this->confirm->getId()])->andWhere(['!=', 'id', $this->getId()])->all();
        //Передаем обновленный список вопросов для добавления в программу
        $queryQuestions = $this->confirm->queryQuestionsGeneralList();
        $queryQuestions[] = $this;

        if ($this->delete()) {
            return ['questions' => $questions, 'queryQuestions' => $queryQuestions];
        }
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
    public function getConfirmId(): int
    {
        return $this->confirm_id;
    }

    /**
     * @param int $confirm_id
     */
    public function setConfirmId(int $confirm_id): void
    {
        $this->confirm_id = $confirm_id;
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
     * @param int $status
     */
    private function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Изменение статуса вопроса
     */
    public function changeStatus(): void
    {
        if ($this->getStatus() === QuestionStatus::STATUS_NOT_STAR){
            $this->setStatus(QuestionStatus::STATUS_ONE_STAR);
        } else {
            $this->setStatus(QuestionStatus::STATUS_NOT_STAR);
        }
    }


    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return ManagerForAnswersAtQuestion
     */
    public function getManagerAnswers(): ManagerForAnswersAtQuestion
    {
        return $this->_manager_answers;
    }

    /**
     *
     */
    public function setManagerAnswers(): void
    {
        $this->_manager_answers = new ManagerForAnswersAtQuestion();
    }

    /**
     * @return CreatorQuestionToGeneralList
     */
    public function getCreatorQuestionToGeneralList(): CreatorQuestionToGeneralList
    {
        return $this->_creator_question_to_general_list;
    }

    /**
     *
     */
    public function setCreatorQuestionToGeneralList(): void
    {
        $this->_creator_question_to_general_list = new CreatorQuestionToGeneralList();
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
}