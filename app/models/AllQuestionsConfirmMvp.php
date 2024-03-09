<?php


namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;

/**
 * Класс хранит информацию в бд о всех вопросах,
 * которые добавлялись на этапе подтверждения mvp-продукта
 *
 * Class AllQuestionsConfirmMvp
 * @package app\models
 *
 * @property int $id                            Идентификатор записи
 * @property string $title                      Описание вопроса
 * @property int $user_id                       Идентификатор пользователя, который добавил вопрос
 * @property int $created_at                    Дата создания
 */
class AllQuestionsConfirmMvp extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'all_questions_confirm_mvp';
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
            1 => ['title' => 'Что понравилось в решении и что нет?'],
            2 => ['title' => 'Вписывается ли предложение в формат вашей деятельности?'],
            3 => ['title' => 'Что неудобно по сравнению с продуктами, которыми пользуются сейчас?'],
            4 => ['title' => 'Какие важные аспекты в продукте не затронуты, которые следовало бы продумать?'],
            5 => ['title' => 'Какая цена решения должна быть по мнению респондентов?'],
            6 => ['title' => 'Сколько сейчас платят?'],
            7 => ['title' => 'Какой бюджет до этого выделяли?'],
            8 => ['title' => 'Заплатили бы вы «X» рублей за продукт, который выполняет задачу «Y»?'],
            9 => ['title' => 'Кто будет финансировать покупку?'],
            10 => ['title' => 'С кем еще мне следует переговорить?'],
            11 => ['title' => 'Вы бы рассказали об этом продукте своим коллегам?'],
            12 => ['title' => 'Вы бы попросили своего руководителя приобрести продукт, который реализует данное ценностное предложение?']
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