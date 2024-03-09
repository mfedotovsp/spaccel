<?php


namespace app\models\forms;

use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\QuestionsConfirmGcp;
use app\models\QuestionsConfirmMvp;
use app\models\QuestionsConfirmProblem;
use app\models\QuestionsConfirmSegment;
use yii\base\Model;

/**
 * Форма редактирования вопроса для интервью на этапе подтверждения гипотезы
 *
 * Class FormUpdateQuestion
 * @package app\models\forms
 *
 * @property int $id                        Идентификатор вопроса
 * @property string $title                  Описание вопроса
 * @property QuestionsConfirmSegment|QuestionsConfirmProblem|QuestionsConfirmGcp|QuestionsConfirmMvp $_question
 * @property ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp $confirm
 */
class FormUpdateQuestion extends Model
{

    public $id;
    public $title;
    private $_question;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'title'], 'required'],
            [['id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'trim'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Описание вопроса',
        ];
    }


    /**
     * FormUpdateQuestion constructor.
     *
     * @param QuestionsConfirmSegment|QuestionsConfirmProblem|QuestionsConfirmGcp|QuestionsConfirmMvp $model
     * @param array $config
     */
    public function __construct($model, array $config = [])
    {
        $this->setQuestion($model);
        $this->setId($model->getId());
        $this->setTitle($model->getTitle());
        parent::__construct($config);
    }


    /**
     * @return ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp
     */
    public function getConfirm()
    {
        return $this->getQuestion()->confirm;
    }


    /**
     * @return array|null
     */
    public function update(): ?array
    {
        $model = $this->getQuestion();
        $model->setTitle($this->getTitle());
        if ($model->save()) {
            $confirm = $model->confirm;
            $questions = $confirm->questions;
            $queryQuestions = $confirm->queryQuestionsGeneralList();

            return ['model' => $model, 'questions' => $questions, 'queryQuestions' => $queryQuestions];
        }
        return null;
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
    public function setId(int $id): void
    {
        $this->id = $id;
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
     * @return QuestionsConfirmGcp|QuestionsConfirmMvp|QuestionsConfirmProblem|QuestionsConfirmSegment
     */
    public function getQuestion()
    {
        return $this->_question;
    }

    /**
     * @param QuestionsConfirmGcp|QuestionsConfirmMvp|QuestionsConfirmProblem|QuestionsConfirmSegment $question
     */
    public function setQuestion($question): void
    {
        $this->_question = $question;
    }

}