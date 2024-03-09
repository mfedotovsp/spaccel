<?php


namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;

/**
 * Класс хранит информацию в бд о всех вопросах,
 * которые добавлялись на этапе подтверждения гипотезы проблемы сегмента
 *
 * Class AllQuestionsConfirmProblem
 * @package app\models
 *
 * @property int $id                            Идентификатор записи
 * @property string $title                      Описание вопроса
 * @property int $user_id                       Идентификатор пользователя, который добавил вопрос
 * @property int $created_at                    Дата создания
 */
class AllQuestionsConfirmProblem extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'all_questions_confirm_problem';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'user_id'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'trim'],
            [['user_id', 'created_at'], 'integer'],
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
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
        ];
    }


    /**
     * Вопросы по-умолчанию
     * @return array
     */
    public static function defaultListQuestions(): array
    {
        return [
            0 => ['title' => 'Чем вы занимаетесь в настоящее время?'],
            1 => ['title' => 'На каком этапе проекта вы находитесь?'],
            2 => ['title' => 'Случалось ли вам столкнуться с …?'],
            3 => ['title' => 'Попадали ли вы в ситуацию ..?'],
            4 => ['title' => 'Как часто с вами происходит ..?'],
            5 => ['title' => 'Когда вы последний раз оказывались в ситуации ..?'],
            6 => ['title' => 'Как на вашу жизнь влияет ..?'],
            7 => ['title' => 'Какие трудности у вас вызывает это решение?'],
            8 => ['title' => 'Что вас не устраивает в нынешнем решении?'],
            9 => ['title' => 'Почему вы поступили именно так?'],
            10 => ['title' => 'Почему вас это беспокоит?'],
            11 => ['title' => 'Каковы последствия этой ситуации?'],
            12 => ['title' => 'С кем еще мне следует переговорить?'],
            13 => ['title' => 'Есть ли еще вопросы, которые мне следовало задать?']
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
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }
}