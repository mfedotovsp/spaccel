<?php


namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс, содержит ключевые слова по поиску экспертов
 *
 * Class KeywordsExpert
 * @package app\models
 *
 * @property int $id                        Идентификатор записи
 * @property int $expert_id                 Идентификатор эксперта в таб.User
 * @property string $description               Ключевые слова
 */
class KeywordsExpert extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'keywords_expert';
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['expert_id', 'description'], 'required'],
            [['expert_id'], 'integer'],
            ['description', 'trim'],
            ['description', 'string', 'max' => 2000],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Ключевые слова'
        ];
    }


    /**
     * @param int $expertId
     */
    public function setExpertId(int $expertId): void
    {
        $this->expert_id = $expertId;
    }


    /**
     * @return int
     */
    public function getExpertId(): int
    {
        return $this->expert_id;
    }


    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * Сохранение ключевых слов эксперта при регистрации
     *
     * @param int $expertId
     * @param string $description
     */
    public static function create(int $expertId, string $description): void
    {
        $keywords = new self();
        $keywords->setExpertId($expertId);
        $keywords->setDescription($description);
        $keywords->save();
    }


    /**
     * Редактирование ключевых слов эксперта
     * @param string $description
     */
    public function edit(string $description): void
    {
        $this->setDescription($description);
        $this->save();
    }
}