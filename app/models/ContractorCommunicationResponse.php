<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс хранит информацию об ответе испонителя на коммуникацию по проекту от проектанта
 *
 * Class ContractorCommunicationResponse
 * @package app\models
 *
 * @property int $id                                            Идентификатор записи
 * @property int $communication_id                              Идентификатор записи в таб. contractor_communications
 * @property int $answer                                        Результат ответа
 * @property string $comment                                    Комментарий к ответу
 */
class ContractorCommunicationResponse extends ActiveRecord
{
    public const POSITIVE_RESPONSE = 5445;
    public const NEGATIVE_RESPONSE = 4554;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['answer', 'communication_id'], 'integer'],
            [['answer', 'communication_id'], 'required'],
            [['comment'], 'string', 'max' => 255],
            ['answer', 'in', 'range' => [
                self::POSITIVE_RESPONSE,
                self::NEGATIVE_RESPONSE
            ]],
        ];
    }

    /**
     * Установить параметры
     *
     * @param int $answer
     * @param string $comment
     * @param int $communication_id
     */
    public function setParams(int $answer, string $comment, int $communication_id): void
    {
        $this->setAnswer($answer);
        $this->setComment($comment);
        $this->setCommunicationId($communication_id);
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
    public function getCommunicationId(): int
    {
        return $this->communication_id;
    }

    /**
     * @param int $communication_id
     */
    public function setCommunicationId(int $communication_id): void
    {
        $this->communication_id = $communication_id;
    }

    /**
     * @return int
     */
    public function getAnswer(): int
    {
        return $this->answer;
    }

    /**
     * @param int $answer
     */
    public function setAnswer(int $answer): void
    {
        $this->answer = $answer;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }


}