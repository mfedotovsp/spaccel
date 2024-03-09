<?php


namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит в бд ответы респондендов на вопросы
 * интервью на этапе подтверждения mvp-продукта
 *
 * Class AnswersQuestionsConfirmMvp
 * @package app\models
 *
 * @property int $id                                            Идентификатор записи в таб. answers_questions_confirm_mvp
 * @property int $question_id                                   Идентификатор записи в таб. questions_confirm_mvp
 * @property int $respond_id                                    Идентификатор записи в таб. responds_mvp
 * @property string $answer                                     Ответ на вопрос
 * @property int|null $deleted_at                               Дата удаления
 *
 * @property QuestionsConfirmMvp $question                      Вопрос
 * @property RespondsMvp $respond                               Респондент
 */
class AnswersQuestionsConfirmMvp extends ActiveRecord
{
    use SoftDeleteModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'answers_questions_confirm_mvp';
    }


    /**
     * Получить объект вопроса
     * @return ActiveQuery
     */
    public function getQuestion(): ActiveQuery
    {
        return $this->hasOne(QuestionsConfirmMvp::class, ['id' => 'question_id']);
    }


    /**
     * Получить объект респондента
     * @return ActiveQuery
     */
    public function getRespond(): ActiveQuery
    {
        return $this->hasOne(RespondsMvp::class, ['id' => 'respond_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['question_id', 'respond_id'], 'required'],
            [['answer'], 'string', 'max' => 1000],
            [['answer'], 'trim'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Описание ответа на вопрос',
        ];
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
    public function getQuestionId(): int
    {
        return $this->question_id;
    }

    /**
     * @param int $question_id
     */
    public function setQuestionId(int $question_id): void
    {
        $this->question_id = $question_id;
    }

    /**
     * @return int
     */
    public function getRespondId(): int
    {
        return $this->respond_id;
    }

    /**
     * @param int $respond_id
     */
    public function setRespondId(int $respond_id): void
    {
        $this->respond_id = $respond_id;
    }

    /**
     * @return string
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     */
    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
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